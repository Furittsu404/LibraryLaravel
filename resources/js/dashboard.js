/**
 * Dashboard Charts Management
 * Handles timeline and pie charts creation, destruction, and updates
 */

export function initializeDashboard(
    timelineData,
    coursesByUsers,
    sexDistribution,
) {
    let timelineChart = null;
    let courseChart = null;
    let sexChart = null;
    let currentTimelineData = timelineData;
    let currentCourseData = coursesByUsers;
    let currentSexData = sexDistribution;

    /**
     * Destroy all existing charts
     */
    function destroyCharts() {
        if (timelineChart) {
            timelineChart.destroy();
            timelineChart = null;
        }
        if (courseChart) {
            courseChart.destroy();
            courseChart = null;
        }
        if (sexChart) {
            sexChart.destroy();
            sexChart = null;
        }
    }

    /**
     * Generate colors for pie chart segments
     */
    function generateColors(count, isDarkMode) {
        const baseColors = [
            { light: "rgba(0, 150, 57, 0.8)", dark: "rgba(0, 179, 71, 0.8)" },
            {
                light: "rgba(59, 130, 246, 0.8)",
                dark: "rgba(96, 165, 250, 0.8)",
            },
            {
                light: "rgba(239, 68, 68, 0.8)",
                dark: "rgba(248, 113, 113, 0.8)",
            },
            {
                light: "rgba(234, 179, 8, 0.8)",
                dark: "rgba(250, 204, 21, 0.8)",
            },
            {
                light: "rgba(168, 85, 247, 0.8)",
                dark: "rgba(192, 132, 252, 0.8)",
            },
            {
                light: "rgba(236, 72, 153, 0.8)",
                dark: "rgba(244, 114, 182, 0.8)",
            },
            {
                light: "rgba(20, 184, 166, 0.8)",
                dark: "rgba(45, 212, 191, 0.8)",
            },
            {
                light: "rgba(249, 115, 22, 0.8)",
                dark: "rgba(251, 146, 60, 0.8)",
            },
        ];

        const colors = [];
        for (let i = 0; i < count; i++) {
            const colorPair = baseColors[i % baseColors.length];
            colors.push(isDarkMode ? colorPair.dark : colorPair.light);
        }
        return colors;
    }

    /**
     * Create or recreate all charts
     */
    function createCharts(
        newTimelineData = null,
        newCourseData = null,
        newSexData = null,
    ) {
        // Always destroy existing charts first
        destroyCharts();

        // Update data if provided
        if (newTimelineData !== null) {
            currentTimelineData = newTimelineData;
        }
        if (newCourseData !== null) {
            currentCourseData = newCourseData;
        }
        if (newSexData !== null) {
            currentSexData = newSexData;
        }

        // Debug logging
        console.log("Creating charts with data:", {
            timelineData: currentTimelineData,
            courseData: currentCourseData,
            sexData: currentSexData,
        });

        // Check if dark mode is active
        const isDarkMode = document.documentElement.classList.contains("dark");
        const textColor = isDarkMode ? "#e5e7eb" : "#374151";
        const gridColor = isDarkMode ? "#374151" : "#e5e7eb";

        // Timeline Chart - check if canvas exists and is visible
        const timelineCanvas = document.getElementById("timelineChart");
        if (timelineCanvas && timelineCanvas.offsetParent !== null) {
            const ctx = timelineCanvas.getContext("2d");
            timelineChart = new Chart(ctx, {
                type: "line",
                data: {
                    labels: currentTimelineData.map((d) => d.date),
                    datasets: [
                        {
                            label: "Logins",
                            data: currentTimelineData.map((d) => d.count),
                            borderColor: isDarkMode
                                ? "rgb(0, 179, 71)"
                                : "rgb(0, 150, 57)",
                            backgroundColor: isDarkMode
                                ? "rgba(0, 179, 71, 0.1)"
                                : "rgba(0, 150, 57, 0.1)",
                            tension: 0.4,
                            fill: true,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false,
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                color: textColor,
                            },
                            grid: {
                                color: gridColor,
                            },
                        },
                        x: {
                            ticks: {
                                color: textColor,
                            },
                            grid: {
                                color: gridColor,
                            },
                        },
                    },
                },
            });
        }

        // Course Pie Chart - check if canvas exists and is visible
        const courseCanvas = document.getElementById("courseChart");
        console.log(
            "Course canvas:",
            courseCanvas,
            "visible:",
            courseCanvas?.offsetParent !== null,
        );
        if (courseCanvas && courseCanvas.offsetParent !== null) {
            const ctx = courseCanvas.getContext("2d");
            const colors = generateColors(currentCourseData.length, isDarkMode);

            console.log(
                "Creating course pie chart with",
                currentCourseData.length,
                "items",
            );
            courseChart = new Chart(ctx, {
                type: "pie",
                data: {
                    labels: currentCourseData.map((c) => c.course),
                    datasets: [
                        {
                            label: "Users",
                            data: currentCourseData.map((c) => c.login_count),
                            backgroundColor: colors,
                            borderColor: isDarkMode ? "#1f2937" : "#ffffff",
                            borderWidth: 2,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: "right",
                            labels: {
                                color: textColor,
                                padding: 10,
                                font: {
                                    size: 11,
                                },
                                boxWidth: 12,
                            },
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    const label = context.label || "";
                                    const value = context.parsed || 0;
                                    const total = context.dataset.data.reduce(
                                        (a, b) => a + b,
                                        0,
                                    );
                                    const percentage = (
                                        (value / total) *
                                        100
                                    ).toFixed(1);
                                    return `${label}: ${value} (${percentage}%)`;
                                },
                            },
                        },
                    },
                },
            });
        }

        // Sex Pie Chart - check if canvas exists and is visible
        const sexCanvas = document.getElementById("sexChart");
        console.log(
            "Sex canvas:",
            sexCanvas,
            "visible:",
            sexCanvas?.offsetParent !== null,
        );
        if (sexCanvas && sexCanvas.offsetParent !== null) {
            const ctx = sexCanvas.getContext("2d");

            console.log(
                "Creating sex pie chart with",
                currentSexData.length,
                "items",
            );

            // Define specific colors for sex (case-insensitive mapping)
            const sexColors = isDarkMode
                ? {
                      male: "rgba(59, 130, 246, 0.8)",
                      Male: "rgba(59, 130, 246, 0.8)",
                      female: "rgba(236, 72, 153, 0.8)",
                      Female: "rgba(236, 72, 153, 0.8)",
                      other: "rgba(168, 85, 247, 0.8)",
                      Other: "rgba(168, 85, 247, 0.8)",
                  }
                : {
                      male: "rgba(37, 99, 235, 0.8)",
                      Male: "rgba(37, 99, 235, 0.8)",
                      female: "rgba(219, 39, 119, 0.8)",
                      Female: "rgba(219, 39, 119, 0.8)",
                      other: "rgba(147, 51, 234, 0.8)",
                      Other: "rgba(147, 51, 234, 0.8)",
                  };

            const colors = currentSexData.map(
                (g) =>
                    sexColors[g.sex] ||
                    (isDarkMode
                        ? "rgba(156, 163, 175, 0.8)"
                        : "rgba(107, 114, 128, 0.8)"),
            );

            sexChart = new Chart(ctx, {
                type: "pie",
                data: {
                    labels: currentSexData.map((g) => g.sex),
                    datasets: [
                        {
                            label: "Users",
                            data: currentSexData.map((g) => g.login_count),
                            backgroundColor: colors,
                            borderColor: isDarkMode ? "#1f2937" : "#ffffff",
                            borderWidth: 2,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: "right",
                            labels: {
                                color: textColor,
                                padding: 15,
                                font: {
                                    size: 12,
                                },
                                boxWidth: 15,
                            },
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    const label = context.label || "";
                                    const value = context.parsed || 0;
                                    const total = context.dataset.data.reduce(
                                        (a, b) => a + b,
                                        0,
                                    );
                                    const percentage = (
                                        (value / total) *
                                        100
                                    ).toFixed(1);
                                    return `${label}: ${value} (${percentage}%)`;
                                },
                            },
                        },
                    },
                },
            });
        }
    }

    // Initialize on DOM ready
    $(document).ready(function () {
        // Small delay to ensure Livewire is ready
        setTimeout(() => {
            createCharts();
        }, 50);

        // Register Livewire event listener after initialization
        if (typeof Livewire !== "undefined") {
            Livewire.on("charts-update", (event) => {
                setTimeout(function () {
                    createCharts(
                        event.timelineData,
                        event.coursesByUsers,
                        event.sexDistribution,
                    );
                }, 100);
            });
        }
    });

    // Handle navigation events
    document.addEventListener("livewire:navigated", function () {
        // Destroy old charts first, then recreate after small delay
        destroyCharts();

        setTimeout(() => {
            // Reset data to initial state
            currentTimelineData = timelineData;
            currentCourseData = coursesByUsers;
            currentSexData = sexDistribution;
            createCharts();
        }, 100);

        // Re-register event listener after navigation
        if (typeof Livewire !== "undefined") {
            Livewire.on("charts-update", (event) => {
                setTimeout(function () {
                    createCharts(
                        event.timelineData,
                        event.coursesByUsers,
                        event.sexDistribution,
                    );
                }, 100);
            });
        }
    });

    // Listen for Livewire initialized event
    document.addEventListener("livewire:initialized", () => {
        // Register the event listener when Livewire is ready
        if (typeof Livewire !== "undefined") {
            Livewire.on("charts-update", (event) => {
                setTimeout(function () {
                    createCharts(
                        event.timelineData,
                        event.coursesByUsers,
                        event.sexDistribution,
                    );
                }, 100);
            });
        }
    });

    // Listen for dark mode toggle
    window.addEventListener("dark-mode-toggled", () => {
        setTimeout(() => createCharts(), 50);
    });

    // Expose createCharts globally
    window.createCharts = function () {
        setTimeout(() => createCharts(), 50);
    };

    // Expose functions for external use
    return {
        createCharts,
        destroyCharts,
    };
}
