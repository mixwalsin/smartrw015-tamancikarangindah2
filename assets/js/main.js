/* ================================================
   Smart RW015 - Custom JavaScript
   ================================================ */

// Initialize AOS
AOS.init({
  duration: 800,
  easing: 'ease-in-out',
  once: true,
  offset: 100
});

// ================================================
// Back to Top Button
// ================================================

const backToTopButton = document.getElementById('backToTop');

window.addEventListener('scroll', () => {
  if (window.pageYOffset > 300) {
    backToTopButton.classList.add('show');
  } else {
    backToTopButton.classList.remove('show');
  }
});

backToTopButton.addEventListener('click', () => {
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  });
});

// ================================================
// Counter Animation for Statistics
// ================================================

function animateCounter() {
  const counters = document.querySelectorAll('.stat-number');
  
  counters.forEach(counter => {
    const target = parseInt(counter.getAttribute('data-target'));
    let current = 0;
    const increment = target / 50;
    const updateCount = () => {
      current += increment;
      if (current < target) {
        counter.textContent = Math.ceil(current);
        requestAnimationFrame(updateCount);
      } else {
        counter.textContent = target;
      }
    };
    updateCount();
  });
}

// Intersection Observer untuk trigger counter
const observerOptions = {
  threshold: 0.1,
  rootMargin: '0px 0px -100px 0px'
};

const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      animateCounter();
      observer.unobserve(entry.target);
    }
  });
}, observerOptions);

const statistikSection = document.getElementById('statistik');
if (statistikSection) {
  observer.observe(statistikSection);
}

// ================================================
// Form Submission Handler
// ================================================

const formKontak = document.getElementById('formKontak');
if (formKontak) {
  formKontak.addEventListener('submit', (e) => {
    e.preventDefault();
    
    // Get form data
    const nama = document.getElementById('nama').value;
    const email = document.getElementById('email').value;
    const telepon = document.getElementById('telepon').value;
    const subjek = document.getElementById('subjek').value;
    const pesan = document.getElementById('pesan').value;
    
    // Simple validation
    if (!nama || !email || !telepon || !subjek || !pesan) {
      alert('Mohon isi semua field');
      return;
    }
    
    // Show success message
    alert('Terima kasih! Pesan Anda telah dikirim. Kami akan segera menghubungi Anda.');
    
    // Reset form
    formKontak.reset();
  });
}

// ================================================
// Navbar Active Link
// ================================================

const navLinks = document.querySelectorAll('.nav-link');
const sections = document.querySelectorAll('section[id]');

window.addEventListener('scroll', () => {
  let current = '';
  
  sections.forEach(section => {
    const sectionTop = section.offsetTop;
    const sectionHeight = section.clientHeight;
    if (pageYOffset >= sectionTop - 200) {
      current = section.getAttribute('id');
    }
  });
  
  navLinks.forEach(link => {
    link.classList.remove('active');
    if (link.getAttribute('href').slice(1) === current) {
      link.classList.add('active');
    }
  });
});

// ================================================
// Smooth Scrolling for Anchor Links
// ================================================

document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function(e) {
    const href = this.getAttribute('href');
    if (href !== '#' && document.querySelector(href)) {
      e.preventDefault();
      document.querySelector(href).scrollIntoView({
        behavior: 'smooth',
        block: 'start'
      });
    }
  });
});

// ================================================
// Modal handling (if needed)
// ================================================

const demoModal = document.getElementById('demoModal');
if (demoModal) {
  demoModal.addEventListener('show.bs.modal', function() {
    console.log('Demo modal opened');
  });
}

// ================================================
// Lazy Load Images (optional)
// ================================================

if ('IntersectionObserver' in window) {
  const images = document.querySelectorAll('img[data-lazy]');
  const imageObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const img = entry.target;
        img.src = img.getAttribute('data-lazy');
        img.removeAttribute('data-lazy');
        imageObserver.unobserve(img);
      }
    });
  });
  
  images.forEach(img => imageObserver.observe(img));
}

// ================================================
// Add animation to navbar on scroll
// ================================================

const navbar = document.querySelector('.navbar');
window.addEventListener('scroll', () => {
  if (window.pageYOffset > 50) {
    navbar.classList.add('navbar-sticky');
  } else {
    navbar.classList.remove('navbar-sticky');
  }
});

// ================================================
// Search functionality (if search is implemented)
// ================================================

function searchContent(query) {
  console.log('Searching for:', query);
  // Implement search logic here
}

// ================================================
// Floating animation for hero shapes
// ================================================

const shapes = document.querySelectorAll('.shape');
if (shapes.length > 0) {
  document.addEventListener('mousemove', (e) => {
    const mouseX = e.clientX / window.innerWidth;
    const mouseY = e.clientY / window.innerHeight;
    
    shapes.forEach((shape, index) => {
      const speed = (index + 1) * 10;
      const x = mouseX * speed;
      const y = mouseY * speed;
      shape.style.transform = `translate(${x}px, ${y}px)`;
    });
  });
}

// ================================================
// Print Page Function
// ================================================

function printPage() {
  window.print();
}

// ================================================
// Share Functions
// ================================================

function shareToWhatsApp() {
  const text = 'Cek Smart RW015 - Sistem Informasi RW Digital Terintegrasi: ' + window.location.href;
  window.open(`https://wa.me/?text=${encodeURIComponent(text)}`, '_blank');
}

function shareToFacebook() {
  window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(window.location.href)}`, '_blank');
}

function shareToTwitter() {
  const text = 'Cek Smart RW015 - Sistem Informasi RW Digital Terintegrasi';
  window.open(`https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(window.location.href)}`, '_blank');
}

// ================================================
// Dark Mode Toggle (optional)
// ================================================

function toggleDarkMode() {
  document.body.classList.toggle('dark-mode');
  localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
}

// Check dark mode preference
if (localStorage.getItem('darkMode') === 'true') {
  document.body.classList.add('dark-mode');
}

// ================================================
// Utility Functions
// ================================================

// Format currency
function formatCurrency(value) {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(value);
}

// Format date
function formatDate(date) {
  return new Intl.DateTimeFormat('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  }).format(date);
}

// Log initialization
console.log('Smart RW015 - Landing Page Loaded Successfully');
console.log('Version: 1.0.0');
console.log('Developed with Bootstrap 5 and AOS');
