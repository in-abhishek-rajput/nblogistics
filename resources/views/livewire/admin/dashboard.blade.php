<div>
    <div class="row g-4">
        <!-- Revenue vs Expense vs Profit Chart -->
        <div class="col-sm-12 col-xl-6">
            <div class="bg-light text-center rounded p-4 shadow-sm h-100">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h6 class="mb-0">Revenue vs Expense vs Profit</h6>
                    <!-- <a href="#">Show All</a> -->
                </div>
                <div style="position: relative; height: 350px; width: 100%;">
                    <canvas id="revenueExpenseChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Trip Status Donut Chart -->
        <div class="col-sm-12 col-xl-6">
            <div class="bg-light text-center rounded p-4 shadow-sm h-100">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h6 class="mb-0">Trip Status</h6>
                    <!-- <a href="#">Show All</a> -->
                </div>
                <div style="position: relative; height: 350px; width: 100%;">
                    <canvas id="tripStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            initCharts();
            
            // Listen for data updates (for Phase 2 dynamic loading)
            Livewire.on('refreshCharts', () => {
                initCharts();
            });
        });

        function initCharts() {
            // Check if Chart is available
            if (typeof Chart === 'undefined') {
                console.error("Chart.js is not loaded.");
                return;
            }

            // Chart global options
            Chart.defaults.color = "#757575";
            Chart.defaults.font.family = "'Inter', sans-serif";

            // Data from Livewire
            const revenueExpenseData = @json($revenueExpenseChartData);
            const tripStatusData = @json($tripStatusChartData);

            // 1. Revenue vs Expense vs Profit Chart
            const ctx1 = document.getElementById("revenueExpenseChart");
            if(ctx1) {
                // Destroy existing instance to prevent duplication
                let chartStatus = Chart.getChart("revenueExpenseChart");
                if (chartStatus != undefined) {
                    chartStatus.destroy();
                }

                new Chart(ctx1.getContext("2d"), {
                    type: "bar",
                    data: {
                        labels: revenueExpenseData.months,
                        datasets: [{
                                label: "Revenue",
                                data: revenueExpenseData.revenue,
                                backgroundColor: "rgba(135, 206, 250, 0.8)"
                            },
                            {
                                label: "Expense",
                                data: revenueExpenseData.expense,
                                backgroundColor: "rgba(255, 179, 71, 0.8)"
                            },
                            {
                                label: "Profit",
                                data: revenueExpenseData.profit,
                                backgroundColor: "rgba(119, 221, 119, 0.8)"
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            }

            // 2. Trip Status Donut Chart
            const ctx2 = document.getElementById("tripStatusChart");
            if(ctx2) {
                let chartStatus2 = Chart.getChart("tripStatusChart");
                if (chartStatus2 != undefined) {
                    chartStatus2.destroy();
                }

                new Chart(ctx2.getContext("2d"), {
                    type: "doughnut",
                    data: {
                        labels: tripStatusData.labels,
                        datasets: [{
                            backgroundColor: [
                                "rgba(135, 206, 250, 0.8)",
                                "rgba(119, 221, 119, 0.8)",
                                "rgba(255, 179, 71, 0.8)",
                                "rgba(255, 105, 97, 0.8)"
                            ],
                            data: tripStatusData.data
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            }
        }
    </script>
    @endpush
</div>
