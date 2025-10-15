// script.js: JS đầy đủ cho interactivity, validation, localStorage 
document.addEventListener('DOMContentLoaded', function() {
    showPage('home'); // Default page
    loadFavorites(); // Load saved items
    updateProfileProgress(); // Init progress
    setupForms(); // Validation & submit handlers
    initCarousel(); // Start carousel
});

// Toggle Pages
function showPage(pageId) {
    document.querySelectorAll('.page').forEach(p => {
        p.classList.remove('active');
        p.classList.add('hidden');
    });
    const targetPage = document.getElementById(pageId);
    targetPage.classList.remove('hidden');
    setTimeout(() => targetPage.classList.add('active'), 10); // Smooth enter
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Mobile Menu
function toggleMobileMenu() {
    const menu = document.getElementById('mobileMenu');
    menu.classList.toggle('hidden');
}

// Carousel Functions
let currentSlideIndex = 0;
const totalSlides = 5;
let carouselInterval;

function initCarousel() {
    showSlide(0);
    carouselInterval = setInterval(nextSlide, 5000);
}

function showSlide(n) {
    document.querySelectorAll('.carousel-item').forEach((slide, index) => {
        slide.classList.toggle('active', index === n);
    });
    document.querySelectorAll('.indicator').forEach((dot, index) => {
        dot.classList.toggle('active', index === n);
    });
    currentSlideIndex = n;
}

function nextSlide() {
    currentSlideIndex = (currentSlideIndex + 1) % totalSlides;
    showSlide(currentSlideIndex);
}

function prevSlide() {
    currentSlideIndex = (currentSlideIndex - 1 + totalSlides) % totalSlides;
    showSlide(currentSlideIndex);
}

function currentSlide(n) {
    clearInterval(carouselInterval);
    showSlide(n - 1);
    carouselInterval = setInterval(nextSlide, 5000); // Restart
}

// Password Toggle
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = input.nextElementSibling.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Form Validation & Submit (LocalStorage placeholder for DB)
function setupForms() {
    // Register Form
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        e.preventDefault();
        if (validateEmail('regEmail') && validatePassword('regPassword')) {
            // Simulate API/DB save
            localStorage.setItem('userEmail', document.getElementById('regEmail').value);
            alert('Đăng ký thành công! OTP đã gửi.'); // Replace with DB call
            showPage('profile');
        }
    });

    // Profile Form
    document.getElementById('profileForm').addEventListener('submit', function(e) {
        e.preventDefault();
        if (validateProfileForm()) {
            saveProfileToStorage();
            alert('Hồ sơ cập nhật thành công!'); // DB integration point
            updateProfileProgress();
        }
    });

    // Apply Form
    document.getElementById('applyForm').addEventListener('submit', function(e) {
        e.preventDefault();
        if (validateApplyForm()) {
            saveApplicationToStorage();
            alert('Đơn nộp thành công! Theo dõi trạng thái.'); // DB + notification
            showPage('apply'); // Stay on page to show status
        }
    });

    // Login (Placeholder)
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        e.preventDefault();
        alert('Đăng nhập thành công!'); // Auth API/DB
        showPage('home');
    });

    // File Upload Previews
    ['cvUpload', 'motivationUpload'].forEach(id => {
        document.getElementById(id).addEventListener('change', function(e) {
            previewFile(e.target, id.replace('Upload', 'Preview'), id.replace('Upload', 'FileName'));
        });
    });
}

function validateEmail(id) {
    const email = document.getElementById(id).value;
    const error = document.getElementById(id + 'Error');
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!regex.test(email)) {
        error.classList.remove('hidden');
        return false;
    }
    error.classList.add('hidden');
    return true;
}

function validatePassword(id) {
    const pass = document.getElementById(id).value;
    const error = document.getElementById(id + 'Error');
    if (pass.length < 8) {
        error.classList.remove('hidden');
        return false;
    }
    error.classList.add('hidden');
    return true;
}

function validateProfileForm() {
    let valid = true;
    // Basic checks - expand for DB schema
    if (!document.getElementById('fullName').value.trim()) {
        document.getElementById('nameError').classList.remove('hidden');
        valid = false;
    }
    if (document.getElementById('gpa').value < 0 || document.getElementById('gpa').value > 4) {
        document.getElementById('gpaError').classList.remove('hidden');
        valid = false;
    }
    return valid;
}

function validateApplyForm() {
    const scholarship = document.getElementById('scholarshipSelect').value;
    const cv = document.getElementById('cvUpload').files.length;
    const motivation = document.getElementById('motivationUpload').files.length;
    if (!scholarship || !cv || !motivation) {
        alert('Vui lòng chọn học bổng và tải đủ tài liệu');
        return false;
    }
    return true;
}

function saveProfileToStorage() {
    const profile = {
        fullName: document.getElementById('fullName').value,
        gpa: document.getElementById('gpa').value,
        researchField: document.getElementById('researchField').value,
        skills: document.getElementById('skills').value,
        achievements: document.getElementById('achievements').value,
        newsletter: document.getElementById('newsletter').checked,
        premiumConsent: document.getElementById('premiumConsent').checked
    };
    localStorage.setItem('userProfile', JSON.stringify(profile)); // DB sync point
}

function loadProfileFromStorage() {
    const saved = localStorage.getItem('userProfile');
    if (saved) {
        const profile = JSON.parse(saved);
        document.getElementById('fullName').value = profile.fullName || '';
        document.getElementById('gpa').value = profile.gpa || '';
        document.getElementById('researchField').value = profile.researchField || '';
        document.getElementById('skills').value = profile.skills || '';
        document.getElementById('achievements').value = profile.achievements || '';
        document.getElementById('newsletter').checked = profile.newsletter || false;
        document.getElementById('premiumConsent').checked = profile.premiumConsent || false;
        updateProfileProgress();
    }
}

function updateProfileProgress() {
    const form = document.getElementById('profileForm');
    const inputs = form.querySelectorAll('input[required], textarea');
    let filled = 0;
    inputs.forEach(input => {
        if (input.value.trim()) filled++;
    });
    const percentage = (filled / inputs.length) * 100;
    document.getElementById('profileProgress').style.width = percentage + '%';
    document.getElementById('progressText').textContent = Math.round(percentage) + '% Hoàn Thành';
}

function resetProfile() {
    document.getElementById('profileForm').reset();
    updateProfileProgress();
    localStorage.removeItem('userProfile');
}

// Search & Filters
const scholarshipsData = [ // Placeholder data - fetch from DB/API
    { id: 1, title: 'Học Bổng Fulbright (Mỹ)', field: 'Social Sciences', country: 'USA', gpaMin: 3.0, type: 'Scholarship', deadline: '2025-12-31', matching: 85 },
    { id: 2, title: 'Nghiên Cứu AI - MIT', field: 'AI', country: 'USA', gpaMin: 3.5, type: 'Research', deadline: '2026-01-15', matching: 92 },
    { id: 3, title: 'Học Bổng Rhodes (Anh)', field: 'Humanities', country: 'UK', gpaMin: 3.7, type: 'Scholarship', deadline: '2025-11-01', matching: 78 },
    { id: 4, title: 'Chương Trình Thạc Sĩ - Oxford', field: 'Economics', country: 'UK', gpaMin: 3.2, type: 'Program', deadline: '2025-10-30', matching: 88 },
    { id: 5, title: 'Nghiên Cứu Sinh Học - Harvard', field: 'Biology', country: 'USA', gpaMin: 3.6, type: 'Research', deadline: '2026-02-20', matching: 91 },
    // Add more for pagination
];

let currentPage = 1;
const itemsPerPage = 6;

function performSearch() {
    applyFilters(); // Trigger filter on search
}

function applyFilters() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const field = document.getElementById('fieldFilter').value.toLowerCase();
    const country = document.getElementById('countryFilter').value.toLowerCase();
    const gpa = parseFloat(document.getElementById('gpaFilter').value) || 0;
    const type = document.getElementById('typeFilter').value.toLowerCase();

    const filtered = scholarshipsData.filter(item => 
        item.title.toLowerCase().includes(searchTerm) &&
        (!field || item.field.toLowerCase().includes(field)) &&
        (!country || item.country.toLowerCase().includes(country)) &&
        item.gpaMin <= gpa &&
        (!type || item.type.toLowerCase().includes(type))
    );

    displayScholarships(filtered);
    updateResultsCount(filtered.length);
}

function displayScholarships(data) {
    const grid = document.getElementById('scholarshipsGrid');
    grid.innerHTML = '';
    const start = (currentPage - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    const paginated = data.slice(start, end);

    if (paginated.length === 0) {
        document.getElementById('noResults').classList.remove('hidden');
        document.getElementById('pagination').classList.add('hidden');
        return;
    }

    document.getElementById('noResults').classList.add('hidden');
    paginated.forEach(item => {
        const card = createScholarshipCard(item);
        grid.appendChild(card);
    });

    updatePagination(data.length);
}

function createScholarshipCard(item) {
    const card = document.createElement('div');
    card.className = 'scholarship-card p-6 rounded-xl relative cursor-pointer hover:shadow-xl transition duration-300';
    card.dataset.id = item.id;
    card.dataset.type = item.type;
    card.innerHTML = `
        <button class="favorite-btn ${localStorage.getItem('favorites')?.includes(item.id.toString()) ? 'liked' : ''}" onclick="toggleFavorite(event, ${item.id})">
            <i class="fas fa-heart"></i>
        </button>
        <h4 class="font-bold text-lg mb-2">${item.title}</h4>
        <p class="text-sm text-gray-600 mb-2"><i class="fas fa-map-marker-alt mr-1"></i>${item.country}</p>
        <p class="text-sm text-gray-600 mb-2"><i class="fas fa-tag mr-1"></i>${item.field} | ${item.type}</p>
        <p class="text-sm text-gray-600 mb-4">GPA Min: ${item.gpaMin} | Hạn: ${item.deadline}</p>
        <div class="flex justify-between items-center">
            <span class="text-green-600 font-bold">Matching: ${item.matching}%</span>
            <button onclick="applyForScholarship(${item.id})" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200 text-sm">Nộp Đơn</button>
        </div>
    `;
    return card;
}

function toggleFavorite(e, id) {
    e.stopPropagation();
    const btn = e.target.closest('.favorite-btn');
    const favorites = JSON.parse(localStorage.getItem('favorites') || '[]');
    const index = favorites.indexOf(id.toString());
    if (index > -1) {
        favorites.splice(index, 1);
        btn.classList.remove('liked');
    } else {
        favorites.push(id.toString());
        btn.classList.add('liked');
    }
    localStorage.setItem('favorites', JSON.stringify(favorites));
    loadFavorites(); // Refresh favorites page if open
}

function applyForScholarship(id) {
    document.getElementById('scholarshipSelect').value = `scholarship-${id}`;
    showPage('apply');
}

function updateResultsCount(count) {
    document.getElementById('resultsCount').textContent = `Hiển thị ${Math.min((currentPage-1)*itemsPerPage + 1, count)}-${Math.min(currentPage*itemsPerPage, count)} của ${count} kết quả`;
}

function updatePagination(total) {
    const totalPages = Math.ceil(total / itemsPerPage);
    document.getElementById('pageInfo').textContent = `Trang ${currentPage} / ${totalPages}`;
    document.getElementById('pagination').classList.toggle('hidden', totalPages <= 1);
}

function prevPage() {
    if (currentPage > 1) {
        currentPage--;
        applyFilters();
    }
}

function nextPage() {
    const totalPages = Math.ceil(scholarshipsData.length / itemsPerPage); // Use filtered length in real
    if (currentPage < totalPages) {
        currentPage++;
        applyFilters();
    }
}

function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('fieldFilter').value = '';
    document.getElementById('countryFilter').value = '';
    document.getElementById('gpaFilter').value = '';
    document.getElementById('typeFilter').value = '';
    applyFilters();
}

// Favorites Page
function loadFavorites() {
    const favorites = JSON.parse(localStorage.getItem('favorites') || '[]');
    const grid = document.getElementById('favoritesGrid');
    const noFav = document.getElementById('noFavorites');
    if (favorites.length === 0) {
        grid.innerHTML = '';
        noFav.classList.remove('hidden');
        return;
    }
    noFav.classList.add('hidden');
    const favScholarships = scholarshipsData.filter(item => favorites.includes(item.id.toString()));
    favScholarships.forEach(item => {
        const card = createScholarshipCard(item);
        card.querySelector('.favorite-btn').classList.add('liked'); // Always liked
        grid.appendChild(card);
    });
}

// Apply Form Helpers
function previewFile(input, previewId, nameId) {
    const file = input.files[0];
    if (file) {
        document.getElementById(nameId).textContent = file.name;
        document.getElementById(previewId).classList.remove('hidden');
    }
}

function removeFile(inputId) {
    document.getElementById(inputId).value = '';
    document.getElementById(inputId + 'Preview').classList.add('hidden');
}

function saveApplicationToStorage() {
    const app = {
        scholarship: document.getElementById('scholarshipSelect').value,
        cv: document.getElementById('cvUpload').files[0]?.name || '',
        motivation: document.getElementById('motivationUpload').files[0]?.name || '',
        notes: document.getElementById('notes').value,
        status: 'pending',
        date: new Date().toISOString().split('T')[0]
    };
    const apps = JSON.parse(localStorage.getItem('applications') || '[]');
    apps.push(app);
    localStorage.setItem('applications', JSON.stringify(apps));
    // Update status list
    updateStatusList();
}

function updateStatusList() {
    const apps = JSON.parse(localStorage.getItem('applications') || '[]');
    const list = document.getElementById('statusList');
    list.innerHTML = apps.map(app => `
        <div class="flex justify-between items-center p-3 bg-white rounded-lg border-l-4 border-yellow-500">
            <span>${app.scholarship} - ${app.status}</span>
            <span class="text-yellow-600"><i class="fas fa-clock"></i> ${app.date}</span>
        </div>
    `).join('');
}

// Premium Modal
function showPremiumModal() {
    document.getElementById('premiumModal').classList.remove('hidden');
}

function closePremiumModal() {
    document.getElementById('premiumModal').classList.add('hidden');
}

// Login Modal (if needed)
function showLoginModal() {
    document.getElementById('loginModal').classList.remove('hidden');
}

function closeLoginModal() {
    document.getElementById('loginModal').classList.add('hidden');
}

// Init on load
loadProfileFromStorage();
applyFilters(); // Load initial search results