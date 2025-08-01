document.addEventListener("DOMContentLoaded", function () {
    const container = document.getElementById("mysteryboxContainer");
    const mode = container?.dataset.mode;
    console.log("MysteryBox.js loaded. Current mode:", mode);

    const backBtn = document.getElementById("backBtn");
    if (backBtn) {
        backBtn.addEventListener("click", function (e) {
            e.preventDefault();
            // PERBAIKAN DI SINI: Logic untuk tombol "Back"
            if (mode === 'Budget') {
                // Jika di mode Budget, kembali ke home
                const homeUrl = backBtn.dataset.homeUrl; // Ambil nilai dari data-home-url
                if (homeUrl) {
                    window.location.href = homeUrl;
                } else {
                    console.error("Home URL data attribute not found on backBtn!");
                    window.location.href = "/"; // Fallback
                }
            } else if (mode === 'Mood') {
                // Jika di mode Mood, kembali ke page sebelumnya (Budget)
                // Ini melibatkan reset session agar halaman Budget muncul lagi
                fetch("/reset-session", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                        "Content-Type": "application/json",
                        Accept: "application/json",
                    },
                }).then(() => {
                    window.location.href = "/mysterybox"; // Kembali ke awal mysterybox (budget)
                }).catch(error => {
                    console.error("Error resetting session:", error);
                    window.location.href = "/mysterybox"; // Fallback
                });
            } else {
                // Default ke home jika mode tidak terdefinisi atau 'Done'
                const homeUrl = backBtn.dataset.homeUrl;
                if (homeUrl) {
                    window.location.href = homeUrl;
                } else {
                    window.location.href = "/"; // Fallback
                }
            }
        });
    }

    function setupSelection(boxes, inputId) {
        const input = document.getElementById(inputId);
        const mysteryBoxImage = document.getElementById("mysteryBoxImage");

        boxes.forEach((box) => {
            box.addEventListener("click", () => {
                boxes.forEach((b) => b.classList.remove("selected"));
                box.classList.add("selected");
                input.value = box.getAttribute("data-value");
                console.log(`Selected ${inputId}:`, input.value);

                if (inputId === "selectedMood") {
                    // Perbaikan: Hapus baris ini karena mystery_box_id tidak lagi dikirim
                    // const mysteryBoxIdInput = document.getElementById("selectedMysteryBoxId");
                    // const moodId = box.getAttribute("data-id");
                    // if (mysteryBoxIdInput && moodId) {
                    //     mysteryBoxIdInput.value = moodId;
                    //     console.log("Selected Mystery Box ID:", moodId);
                    // }

                    if (box.dataset.img && mysteryBoxImage) {
                        mysteryBoxImage.src = box.dataset.img;
                    }
                }
            });
        });
    }

    const warningModalElement = document.getElementById("warningModal");
    const warningModal = new bootstrap.Modal(warningModalElement);

    warningModalElement.addEventListener("hidden.bs.modal", function () {
        console.log("Warning Modal hidden event triggered.");
        const backdrop = document.querySelector(".modal-backdrop");
        if (backdrop) {
            backdrop.remove();
        }
    });

    // Mengganti doneModal dengan successAddToCartModal
    const successAddToCartModalElement = document.getElementById("successAddToCartModal");
    const successAddToCartModal = new bootstrap.Modal(successAddToCartModalElement);


    if (mode === "Budget") {
        const budgetBoxes = document.querySelectorAll(".budget_box");
        if (budgetBoxes.length > 0) {
            setupSelection(budgetBoxes, "selectedBudget");
        } else {
            console.warn("WARNING: No .budget_box elements found for Budget mode.");
        }

        const budgetForm = document.getElementById("budgetForm");
        if (budgetForm) {
            budgetForm.addEventListener("submit", function (e) {
                if (!document.getElementById("selectedBudget").value) {
                    e.preventDefault();
                    console.log("Budget not selected. Showing warning modal.");
                    const warningModalTextElement = warningModalElement.querySelector("p");
                    if (warningModalTextElement) {
                        const chooseBudgetText = document.getElementById("chooseBudgetText")?.textContent || "Please choose your budget";
                        warningModalTextElement.textContent = chooseBudgetText;
                    }
                    warningModal.show();
                } else {
                    console.log("Budget selected. Submitting budgetForm.");
                }
            });
        } else {
            console.error("ERROR: budgetForm not found! Ensure your HTML has an element with id='budgetForm'.");
        }
    }

    if (mode === "Mood") {
        const moodBoxes = document.querySelectorAll(".mood_box");
        setupSelection(moodBoxes, "selectedMood");

        const moodForm = document.getElementById("moodForm");

        moodForm.addEventListener("submit", function (e) {
            e.preventDefault();

            const selectedMood = document.getElementById("selectedMood").value;
            // const selectedBoxId = document.getElementById("selectedMysteryBoxId").value; // Hapus ini

            if (!selectedMood) { // Cukup cek selectedMood
                const warningModalTextElement = warningModalElement.querySelector("p");
                if (warningModalTextElement) {
                    warningModalTextElement.textContent = document.getElementById("chooseBudgetText")?.textContent || "Please choose your mood";
                }
                warningModal.show();
                return;
            }

            // Dapatkan CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

            // Buat objek FormData
            const formData = new FormData();
            formData.append('mood', selectedMood);
            // Tidak perlu menambahkan mystery_box_id ke FormData jika MysteryBox dicari di backend
            // berdasarkan mood dan budget dari session.

            fetch("/set-mood", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrfToken, // Tetap kirim CSRF token
                    "Content-Type": "application/json", // Sesuaikan dengan tipe data yang dikirim (JSON)
                    Accept: "application/json",
                },
                body: JSON.stringify({ mood: selectedMood }), // Kirim hanya mood
            })
                .then((res) => {
                    console.log("Fetch response received. Status:", res.status, res.ok);
                    if (!res.ok) {
                        // Coba parse error response jika ada
                        return res.json().then(errorData => {
                            console.error("Error response from server:", errorData);
                            throw new Error(errorData.message || "Bad request");
                        });
                    }
                    return res.json();
                })
                .then((data) => {
                    console.log("Parsed JSON data (success branch):", data);
                    if (data.success) {
                        successAddToCartModal.show();
                    } else {
                        // Tampilkan pesan error dari backend
                        const warningModalTextElement = warningModalElement.querySelector("p");
                        if (warningModalTextElement) {
                            warningModalTextElement.textContent = data.message || "Failed to add to cart. Please try again.";
                        }
                        warningModal.show();
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        }
                    }
                })
                .catch((err) => {
                    console.error("Error during mood form submission:", err);
                    const warningModalTextElement = warningModalElement.querySelector("p");
                    if (warningModalTextElement) {
                        warningModalTextElement.textContent = err.message || "Failed to add to cart: An unexpected error occurred.";
                    }
                    warningModal.show();
                });
        });
    }
});
