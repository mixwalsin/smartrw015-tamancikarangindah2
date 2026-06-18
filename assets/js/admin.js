// ================================================
// Smart RW015 - Admin Dashboard JavaScript
// ================================================

// Initialize
document.addEventListener('DOMContentLoaded', function() {
  initSidebar();
  initCharts();
  initUI();
});

// ================================================
// Sidebar Toggle
// ================================================

function initSidebar() {
  const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
  const sidebarToggle = document.getElementById('sidebarToggle');
  const sidebar = document.getElementById('sidebar');

  if (sidebarToggleBtn) {
    sidebarToggleBtn.addEventListener('click', () => {
      sidebar.classList.toggle('show');
    });
  }

  if (sidebarToggle) {
    sidebarToggle.addEventListener('click', () => {
      sidebar.classList.remove('show');
    });
  }

  // Close sidebar when clicking outside on mobile
  document.addEventListener('click', (e) => {
    if (window.innerWidth <= 768) {
      if (!sidebar.contains(e.target) && !sidebarToggleBtn?.contains(e.target)) {
        sidebar.classList.remove('show');
      }
    }
  });
}

// ================================================
// Charts Initialization
// ================================================

function initCharts() {
  // Chart 1: Distribusi Penduduk per RT
  const chartDistribusiRTCtx = document.getElementById('chartDistribusiRT');
  if (chartDistribusiRTCtx) {
    new Chart(chartDistribusiRTCtx, {
      type: 'bar',
      data: {
        labels: ['RT001', 'RT002', 'RT003', 'RT004', 'RT005', 'RT006', 'RT007'],
        datasets: [
          {
            label: 'Jumlah Penduduk',
            data: [765, 850, 722, 680, 638, 595, 510],
            backgroundColor: [
              '#0D6EFD',
              '#198754',
              '#0DCAF0',
              '#FFC107',
              '#DC3545',
              '#6F42C1',
              '#20C997'
            ],
            borderRadius: 6,
            borderSkipped: false,
            barPercentage: 0.7
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              color: 'rgba(0, 0, 0, 0.06)',
              drawBorder: false
            },
            ticks: {
              callback: function(value) {
                return value;
              }
            }
          },
          x: {
            grid: {
              display: false,
              drawBorder: false
            }
          }
        }
      }
    });
  }

  // Chart 2: Demografi Penduduk
  const chartDemografiCtx = document.getElementById('chartDemografi');
  if (chartDemografiCtx) {
    new Chart(chartDemografiCtx, {
      type: 'doughnut',
      data: {
        labels: ['Pria', 'Wanita'],
        datasets: [
          {
            data: [2150, 2100],
            backgroundColor: ['#0D6EFD', '#DC3545'],
            borderColor: 'white',
            borderWidth: 2
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              padding: 15,
              font: {
                size: 12,
                weight: '500'
              }
            }
          }
        }
      }
    });
  }

  // Chart 3: Status Perkawinan
  const chartPerkawinanCtx = document.getElementById('chartPerkawinan');
  if (chartPerkawinanCtx) {
    new Chart(chartPerkawinanCtx, {
      type: 'pie',
      data: {
        labels: ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'],
        datasets: [
          {
            data: [1080, 2650, 350, 170],
            backgroundColor: ['#0DCAF0', '#198754', '#FFC107', '#DC3545'],
            borderColor: 'white',
            borderWidth: 2
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              padding: 15,
              font: {
                size: 12,
                weight: '500'
              }
            }
          }
        }
      }
    });
  }

  // Chart 4: Kas RW Trend
  const chartKasRWCtx = document.getElementById('chartKasRW');
  if (chartKasRWCtx) {
    new Chart(chartKasRWCtx, {
      type: 'line',
      data: {
        labels: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'],
        datasets: [
          {
            label: 'Pemasukan',
            data: [12000000, 14000000, 13500000, 15000000, 14500000, 16000000],
            borderColor: '#198754',
            backgroundColor: 'rgba(25, 135, 84, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            pointRadius: 4,
            pointBackgroundColor: '#198754',
            pointBorderColor: 'white',
            pointBorderWidth: 2
          },
          {
            label: 'Pengeluaran',
            data: [8000000, 9000000, 8500000, 10000000, 9500000, 11000000],
            borderColor: '#DC3545',
            backgroundColor: 'rgba(220, 53, 69, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            pointRadius: 4,
            pointBackgroundColor: '#DC3545',
            pointBorderColor: 'white',
            pointBorderWidth: 2
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: {
            position: 'top',
            labels: {
              padding: 15,
              font: {
                size: 12,
                weight: '500'
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              color: 'rgba(0, 0, 0, 0.06)',
              drawBorder: false
            },
            ticks: {
              callback: function(value) {
                return 'Rp ' + formatCurrency(value);
              }
            }
          },
          x: {
            grid: {
              display: false,
              drawBorder: false
            }
          }
        }
      }
    });
  }
}

// ================================================
// UI Initialization
// ================================================

function initUI() {
  // Active menu highlighting based on current page
  const currentPage = window.location.pathname.split('/').pop();
  const navLinks = document.querySelectorAll('.sidebar-nav .nav-link');
  
  navLinks.forEach(link => {
    const href = link.getAttribute('href');
    if (href === currentPage || (currentPage === '' && href === 'index.html')) {
      link.classList.add('active');
      // Open parent collapse if nested
      const parentCollapse = link.closest('.collapse');
      if (parentCollapse) {
        parentCollapse.classList.add('show');
        const parentLink = document.querySelector(`[href="#${parentCollapse.id}"]`);
        if (parentLink) parentLink.classList.remove('collapsed');
      }
    } else {
      link.classList.remove('active');
    }
  });

  // Tooltip initialization (Bootstrap)
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });
}

// ================================================
// Utility Functions
// ================================================

// Format currency
function formatCurrency(value) {
  return new Intl.NumberFormat('id-ID', {
    style: 'decimal',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(value / 1000000) + ' Jt';
}

// Format date
function formatDate(date) {
  return new Intl.DateTimeFormat('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  }).format(date);
}

// Format number
function formatNumber(num) {
  return new Intl.NumberFormat('id-ID').format(num);
}

// ================================================
// Export Functions
// ================================================

function exportToCSV() {
  console.log('Export to CSV');
  alert('Fitur export CSV sedang dalam pengembangan');
}

function exportToExcel() {
  console.log('Export to Excel');
  alert('Fitur export Excel sedang dalam pengembangan');
}

function exportToPDF() {
  console.log('Export to PDF');
  alert('Fitur export PDF sedang dalam pengembangan');
}

function printPage() {
  window.print();
}

// ================================================
// Real-time Update Simulation
// ================================================

function simulateRealTimeUpdates() {
  setInterval(() => {
    // Simulate updating stat cards
    const statCards = document.querySelectorAll('.stat-card');
    if (statCards.length > 0) {
      // Could update values here if real API is connected
    }
  }, 30000); // Update every 30 seconds
}

// Uncomment to enable real-time updates
// simulateRealTimeUpdates();

// ================================================
// Logging
// ================================================

console.log('Smart RW015 - Admin Dashboard Loaded');
console.log('Version: 1.0.0');
console.log('Environment: Development');
