document.addEventListener("DOMContentLoaded", function () {
    const container = document.getElementById("mysteryboxContainer");
    const mode = container?.dataset.mode;
    console.log("MysteryBox.js loaded. Current mode:", mode);

    const backBtn = document.getElementById("backBtn");
    if (backBtn) {
        backBtn.addEventListener("click", function (e) {
            e.preventDefault();
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
                window.location.href = "/mysterybox";
            });
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
                    const mysteryBoxIdInput = document.getElementById("selectedMysteryBoxId");
                    const moodId = box.getAttribute("data-id");
                    if (mysteryBoxIdInput && moodId) {
                        mysteryBoxIdInput.value = moodId;
                        console.log("Selected Mystery Box ID:", moodId);
                    }

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

    const doneModalElement = document.getElementById("doneModal");
    const doneModal = new bootstrap.Modal(doneModalElement);

    if (doneModalElement) {
        doneModalElement.addEventListener("hidden.bs.modal", function () {
            console.log("Done Modal hidden event triggered. Redirecting to /cart...");
            window.location.href = "/cart";
        });

        const confirmBtn = doneModalElement.querySelector(".btn-confirm");
        if (confirmBtn) {
            confirmBtn.addEventListener("click", function () {
                console.log("Confirm button clicked. Hiding done modal.");
                doneModal.hide();
            });
        }
    } else {
        console.error("ERROR: doneModalElement not found! Ensure your HTML has an element with id='doneModal'.");
    }

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
                        warningModalTextElement.textContent = "Please choose a budget first!";
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

        const modalDoneElement = document.getElementById("doneModal");
        const modalDone = new bootstrap.Modal(modalDoneElement);

        moodForm.addEventListener("submit", function (e) {
            e.preventDefault();

            const selectedMood = document.getElementById("selectedMood").value;
            const selectedBoxId = document.getElementById("selectedMysteryBoxId").value;

            if (!selectedMood || !selectedBoxId) {
                warningModal.show();
                return;
            }

            fetch("/set-mood", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                    "Content-Type": "application/json",
                    Accept: "application/json",
                },
                body: JSON.stringify({ mood: selectedMood, mystery_box_id: selectedBoxId }),
            })
                .then((res) => {
                    console.log("Fetch response received. Status:", res.status, res.ok);
                    if (!res.ok) throw new Error("Bad request");
                    return res.json();
                })
                .then((data) => {
                    console.log("Parsed JSON data (success branch):", data);
                    if (data.success) {
                        modalDone.show();
                        setTimeout(() => {
                            window.location.href = "/cart";
                        }, 2500);
                    }
                })
                .catch((err) => {
                    console.error("Error during mood form submission:", err);
                });
        });
    }
});
