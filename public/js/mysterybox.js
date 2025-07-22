// document.addEventListener("DOMContentLoaded", function () {
//     const mode = document.body.dataset.mode; // ambil mode dari data attribute blade (wajib)

//     // Back button for Mood step
//     const backBtn = document.getElementById("backBtn");
//     if (backBtn) {
//         backBtn.addEventListener("click", function (e) {
//             e.preventDefault();
//             fetch("/reset-session", {
//                 method: "POST",
//                 headers: {
//                     "X-CSRF-TOKEN": document
//                         .querySelector('meta[name="csrf-token"]')
//                         .getAttribute("content"),
//                     "Content-Type": "application/json",
//                     Accept: "application/json",
//                 },
//             }).then(() => {
//                 window.location.href = "/mysterybox";
//             });
//         });
//     }

//     const budgetBoxes = document.querySelectorAll(".budget_box");
//     const moodBoxes = document.querySelectorAll(".mood_box");

//     function setupSelection(boxes, inputId) {
//         const input = document.getElementById(inputId);

//         boxes.forEach((box) => {
//             box.addEventListener("click", () => {
//                 boxes.forEach((b) => b.classList.remove("selected"));
//                 box.classList.add("selected");
//                 input.value = box.getAttribute("data-value");

//                 // Update mystery box image on mood selection
//                 if (inputId === "selectedMood") {
//                     const mysteryBoxImage =
//                         document.getElementById("mysteryBoxImage");
//                     const imgUrl = box.dataset.img;
//                     if (imgUrl) {
//                         mysteryBoxImage.src = imgUrl;
//                     }
//                 }
//             });
//         });
//     }

//     if (mode === "Budget") {
//         setupSelection(budgetBoxes, "selectedBudget");

//         const budgetForm = document.getElementById("budgetForm");
//         const warningModal = new bootstrap.Modal(
//             document.getElementById("warningModal")
//         );

//         budgetForm.addEventListener("submit", function (e) {
//             if (!document.getElementById("selectedBudget").value) {
//                 e.preventDefault();
//                 warningModal.show();
//             }
//         });
//     } else if (mode === "Mood") {
//         setupSelection(moodBoxes, "selectedMood");

//         const moodForm = document.getElementById("moodForm");
//         const doneModal = new bootstrap.Modal(
//             document.getElementById("doneModal")
//         );
//         const warningModal = new bootstrap.Modal(
//             document.getElementById("warningModal")
//         );

//         moodForm.addEventListener("submit", function (e) {
//             if (!document.getElementById("selectedMood").value) {
//                 e.preventDefault();
//                 warningModal.show();
//             } else {
//                 e.preventDefault();
//                 doneModal.show();
//             }
//         });
//     }
// });

document.addEventListener("DOMContentLoaded", function () {
    const container = document.getElementById("mysteryboxContainer");
    const mode = container?.dataset.mode;

    // Back button to reset
    const backBtn = document.getElementById("backBtn");
    if (backBtn) {
        backBtn.addEventListener("click", function (e) {
            e.preventDefault();
            fetch("/reset-session", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
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

                // Update image if it's mood
                if (inputId === "selectedMood" && box.dataset.img) {
                    mysteryBoxImage.src = box.dataset.img;
                }
            });
        });
    }

    if (mode === "Budget") {
        const budgetBoxes = document.querySelectorAll(".budget_box");
        setupSelection(budgetBoxes, "selectedBudget");

        const budgetForm = document.getElementById("budgetForm");
        const warningModal = new bootstrap.Modal(document.getElementById("warningModal"));

        budgetForm.addEventListener("submit", function (e) {
            if (!document.getElementById("selectedBudget").value) {
                e.preventDefault();
                warningModal.show();
            }
        });
    }

    if (mode === "Mood") {
        const moodBoxes = document.querySelectorAll(".mood_box");
        setupSelection(moodBoxes, "selectedMood");

        const moodForm = document.getElementById("moodForm");
        const warningModal = new bootstrap.Modal(document.getElementById("warningModal"));

        moodForm.addEventListener("submit", function (e) {
            if (!document.getElementById("selectedMood").value) {
                e.preventDefault();
                warningModal.show();
            }
        });
    }
});
