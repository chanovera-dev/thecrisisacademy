(function () {
    "use strict";

    function initSimulatorCardGlow() {
        // Remove .blue-background-00 class from all .sdc-section elements
        var sdcSections = document.querySelectorAll(".sdc-section.blue-background-00");
        sdcSections.forEach(function (section) {
            section.classList.remove("blue-background-00");
        });

        var container = document.querySelector(".sdc-standalone-dialog, #sdc-page-section, .sdc-simulator-container");
        if (!container) return;

        var cardSelector = ".blue-background-00, .sdc-card, .glass-border-bright, .sdc-metric-card, .sdc-summary-card, .sdc-status-card, .sdc-protocol-card, .sdc-scenario-card, .sdc-dashboard-chart, .sdc-participant-card, .sdc-active-card";

        var updateCoords = function (card, e) {
            if (window.innerWidth <= 768) return;
            var rect = card.getBoundingClientRect();
            var x = e.clientX - rect.left;
            var y = e.clientY - rect.top;
            card.style.setProperty("--mouse-x", x + "px");
            card.style.setProperty("--mouse-y", y + "px");
        };

        var resetCoords = function (card) {
            card.style.setProperty("--mouse-x", "-999px");
            card.style.setProperty("--mouse-y", "-999px");
        };

        // Direct event listener on existing cards
        var cards = container.querySelectorAll(cardSelector);
        cards.forEach(function (card) {
            card.addEventListener("mousemove", function (e) {
                updateCoords(card, e);
            });
            card.addEventListener("mouseleave", function () {
                resetCoords(card);
            });
        });

        // Delegated listener scoped strictly to the simulator container for step changes (steps 1 to 7)
        container.addEventListener("mousemove", function (e) {
            if (window.innerWidth <= 768) return;
            var activeCard = e.target.closest(cardSelector);
            if (activeCard) {
                updateCoords(activeCard, e);
            }
        }, { passive: true });

        container.addEventListener("mouseout", function (e) {
            var activeCard = e.target.closest(cardSelector);
            if (activeCard && !activeCard.contains(e.relatedTarget)) {
                resetCoords(activeCard);
            }
        }, { passive: true });

        // Wake up initial step animatables
        var activeSection = container.querySelector(".sdc-section.active, .sdc-section[data-step=\'1\']");
        if (activeSection) {
            var animatables = activeSection.querySelectorAll(".sdc-animate");
            if (typeof window.animateIn === "function") {
                window.animateIn(animatables, ["animate-in"], { threshold: 0.01, stagger: 80 });
            } else {
                animatables.forEach(function (el) {
                    el.classList.add("animate-in");
                });
            }
        }
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", initSimulatorCardGlow);
    } else {
        initSimulatorCardGlow();
    }
})();