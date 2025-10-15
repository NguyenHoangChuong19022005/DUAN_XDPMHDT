
document.addEventListener('DOMContentLoaded', function() {
    showPage('dashboard');
    loadOrgProfile();
    loadApplications();
    loadChats();
    setupForms();
});

// Toggle Pages
function showPage(pageId) {
    document.querySelectorAll('.page').forEach(p => {
        p.classList.remove('active');
        p.classList.add('hidden');
    });
    const target = document.getElementById(pageId);
    target.classList.remove('hidden');
    setTimeout(() => target.classList.add('active'), 10);
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Mobile & Dropdown
function toggleMobileMenu() {
    document.getElementById('mobileMenu').classList.toggle('hidden');
}

function toggleRoleDropdown() {
    document.getElementById('roleDropdown').classList.toggle('hidden');
}

// Logout
function logout() {
    if (confirm('Xác nhận đăng xuất?')) {
        localStorage.removeItem('orgProfile');
        window.location.href = '../index.html'; // Redirect to student
    }
}

// Post Opportunity Form
function setupForms() {
    document.getElementById('postForm').addEventListener('submit', function(e) {
        e.preventDefault();
        if (validatePostForm()) {
            saveOpportunity();
            alert('Cơ hội đăng thành công!');
        }
    });

    document.getElementById('orgProfileForm').addEventListener('submit', function(e) {
        e.preventDefault();
        saveOrgProfile();
        alert('Hồ sơ cập nhật!');
    });

    // Image Preview
    document.getElementById('imageUpload').addEventListener('change', function(e) {
        previewImage(e.target.files[0]);
    });
}

function validatePostForm() {
    // Basic validation - expand for DB
    let valid = true;
    const title = document.getElementById('title').value.trim();
    if (!title) {
        document.getElementById('titleError').classList.remove('hidden');
        valid = false;
    }
    const desc = document.getElementById('description').value.trim();
    if (desc.length < 50) {
        document.getElementById('descError').classList.remove('hidden');
        valid = false;
    }
    const deadline = new Date(document.getElementById('deadline').value);
    if (deadline < new Date()) {
        document.getElementById('deadlineError').classList.remove('hidden');
        valid = false;
    }
    return valid;
}

function saveOpportunity() {
    const opp = {
        title: document.getElementById('title').value,
        type: document.getElementById('type').value,
        description: document.getElementById('description').value,
        country: document.getElementById('country').value,
        deadline: document.getElementById('deadline').value,
        gpaMin: document.getElementById('gpaMin').value,
        requiredSkills: document.getElementById('requiredSkills').value,
        experienceReq: document.getElementById('experienceReq').value,
        aiMatching: document.getElementById('aiMatching').checked,
        image: document.getElementById('imageUpload').files[0]?.name || '',
        date: new Date().toISOString().split('T')[0]
    };
    const opps = JSON.parse(localStorage.getItem('opportunities') || '[]');
    opps.push(opp);
    localStorage.setItem('opportunities', JSON.stringify(opps));
    // DB sync point
}

function previewImage(file) {
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
}

function removeImage() {
    document.getElementById('imageUpload').value = '';
    document.getElementById('imagePreview').classList.add('hidden');
}

// Org Profile
function loadOrgProfile() {
    const saved = localStorage.getItem('orgProfile');
    if (saved) {
        const profile = JSON.parse(saved);
        document.getElementById('orgName').value = profile.name || '';
        document.getElementById('orgEmail').value = profile.email || '';
        document.getElementById('orgDescription').value = profile.description || '';
        document.getElementById('orgWebsite').value = profile.website || '';
        document.getElementById('orgPremium').checked = profile.premium || false;
    }
}

function saveOrgProfile() {
    const profile = {
        name: document.getElementById('orgName').value,
        email: document.getElementById('orgEmail').value,
        description: document.getElementById('orgDescription').value,
        website: document.getElementById('orgWebsite').value,
        premium: document.getElementById('orgPremium').checked
    };
    localStorage.setItem('orgProfile', JSON.stringify(profile)); // DB sync
}

// Applications
const applicationsData = [ // Placeholder - from DB
    { id: 1, name: 'Nguyễn Văn A', email: 'a@example.com', date: '2025-10-10', gpa: 3.8, matching: 92, status: 'pending', opportunity: 'Fulbright', documents: { cv: 'cv.pdf', motivation: 'letter.pdf' } },
    { id: 2, name: 'Trần Thị B', email: 'b@example.com', date: '2025-10-12', gpa: 3.5, matching: 78, status: 'interview', opportunity: 'MIT Lab', documents: { cv: 'cv2.pdf', motivation: 'letter2.pdf' } },
    // Add more
];

let appCurrentPage = 1;
const appItemsPerPage = 5;

function loadApplications() {
    displayApplications(applicationsData);
}

function applyAppFilters() {
    const search = document.getElementById('appSearch').value.toLowerCase();
    const status = document.getElementById('appStatusFilter').value.toLowerCase();
    const filtered = applicationsData.filter(app => 
        app.name.toLowerCase().includes(search) || app.email.toLowerCase().includes(search) ||
        (!status || app.status === status)
    );
    displayApplications(filtered);
}

function displayApplications(data) {
    const tbody = document.getElementById('appTableBody');
    tbody.innerHTML = '';
    const start = (appCurrentPage - 1) * appItemsPerPage;
    const end = start + appItemsPerPage;
    const paginated = data.slice(start, end);

    paginated.forEach(app => {
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50 transition duration-150';
        row.dataset.id = app.id;
        row.innerHTML = `
            <td class="px-6 py-4">
                <div class="flex items-center">
                    <div class="h-10 w-10 bg-blue-200 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-user text-blue-600"></i>
                    </div>
                    <div>
                        <div class="font-bold">${app.name}</div>
                        <div class="text-sm text-gray-500">${app.opportunity}</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 text-sm">${app.email}</td>
            <td class="px-6 py-4 text-sm">${app.date}</td>
            <td class="px-6 py-4">
                <div>GPA: ${app.gpa}</div>
                <div class="text-green-600 font-bold">Matching: ${app.matching}%</div>
            </td>
            <td class="px-6 py-4">
                <span class="status-badge px-2 py-1 text-xs font-bold rounded-full ${getStatusClass(app.status)}">${getStatusText(app.status)}</span>
            </td>
            <td class="px-6 py-4 text-sm">
                <button onclick="viewDocument(${app.id}, 'cv')" class="text-blue-600 underline mr-2">CV</button>
                <button onclick="viewDocument(${app.id}, 'motivation')" class="text-blue-600 underline">Thư Động Lực</button>
            </td>
            <td class="px-6 py-4">
                <button onclick="openAppModal(${app.id})" class="text-indigo-600 hover:text-indigo-900 mr-3">Chi Tiết</button>
                <select onchange="updateAppStatus(${app.id}, this.value)" class="text-sm border rounded p-1">
                    <option value="${app.status}">Cập Nhật</option>
                    <option value="interview">Mời Phỏng Vấn</option>
                    <option value="accepted">Chấp Nhận</option>
                    <option value="rejected">Từ Chối</option>
                </select>
            </td>
        `;
        tbody.appendChild(row);
    });

    updateAppPagination(data.length);
}

function getStatusClass(status) {
    const classes = { pending: 'bg-yellow-100 text-yellow-800', interview: 'bg-orange-100 text-orange-800', accepted: 'bg-green-100 text-green-800', rejected: 'bg-red-100 text-red-800' };
    return classes[status] || 'bg-gray-100 text-gray-800';
}

function getStatusText(status) {
    const texts = { pending: 'Chờ Xử Lý', interview: 'Mời Phỏng Vấn', accepted: 'Chấp Nhận', rejected: 'Từ Chối' };
    return texts[status] || status;
}

function openAppModal(id) {
    const app = applicationsData.find(a => a.id === id);
    if (app) {
        document.getElementById('modalAppTitle').textContent = `${app.name} - ${app.opportunity}`;
        document.getElementById('modalAppContent').innerHTML = `
            <p><strong>Email:</strong> ${app.email}</p>
            <p><strong>GPA:</strong> ${app.gpa} | <strong>Matching:</strong> ${app.matching}%</p>
            <p><strong>Ngày Nộp:</strong> ${app.date}</p>
            <p><strong>Tài Liệu:</strong> CV & Thư Động Lực đã tải lên</p>
        `;
        document.getElementById('appDetailModal').classList.remove('hidden');
    }
}

function closeAppModal() {
    document.getElementById('appDetailModal').classList.add('hidden');
}

function updateAppStatus(id, status) {
    const app = applicationsData.find(a => a.id === id);
    if (app) {
        app.status = status;
        localStorage.setItem('applications', JSON.stringify(applicationsData)); // DB update
        loadApplications(); // Refresh
        alert(`Cập nhật trạng thái: ${getStatusText(status)}`);
    }
}

function viewDocument(id, type) {
    alert(`Xem tài liệu ${type} cho đơn ${id} (Tích hợp viewer sau)`);
}

function updateAppPagination(total) {
    const pages = Math.ceil(total / appItemsPerPage);
    document.getElementById('appPageInfo').textContent = `Trang ${appCurrentPage} / ${pages}`;
    document.getElementById('appResultsCount').textContent = `Hiển thị ${(appCurrentPage-1)*appItemsPerPage + 1}-${Math.min(appCurrentPage*appItemsPerPage, total)} của ${total} kết quả`;
}

function prevAppPage() {
    if (appCurrentPage > 1) {
        appCurrentPage--;
        applyAppFilters();
    }
}

function nextAppPage() {
    const pages = Math.ceil(applicationsData.length / appItemsPerPage);
    if (appCurrentPage < pages) {
        appCurrentPage++;
        applyAppFilters();
    }
}

// Communication
const chatsData = [ // Placeholder
    { id: 1, user: 'Nguyễn Văn A (Fulbright)', unread: 2, lastMsg: 'Cảm ơn phản hồi!' },
    { id: 2, user: 'Trần Thị B (MIT Lab)', unread: 0, lastMsg: 'Sẵn sàng phỏng vấn' }
];

function loadChats() {
    const list = document.getElementById('chatList');
    list.innerHTML = chatsData.map(chat => `
        <div class="p-3 bg-gray-100 rounded-lg cursor-pointer hover:bg-gray-200 transition duration-200 flex justify-between items-center" onclick="selectChat(${chat.id})">
            <div>
                <div class="font-bold">${chat.user}</div>
                <div class="text-sm text-gray-600">${chat.lastMsg}</div>
            </div>
            ${chat.unread > 0 ? `<span class="bg-red-500 text-white px-2 py-1 rounded-full text-xs">${chat.unread}</span>` : ''}
        </div>
    `).join('');
}

function selectChat(id) {
    const chat = chatsData.find(c => c.id === id);
    if (chat) {
        document.getElementById('activeChatTitle').innerHTML = `${chat.user} <span class="text-sm text-gray-500">(${chat.unread > 0 ? '2 tin mới' : 'Đã đọc'})</span>`;
        document.getElementById('chatMessages').innerHTML = `
            <div class="chat-message sent mb-4">Xin chào, chúng tôi quan tâm đến hồ sơ của bạn...</div>
            <div class="chat-message received mb-4">Cảm ơn! Tôi sẵn sàng phỏng vấn.</div>
        `;
        if (chat.unread > 0) {
            chat.unread = 0;
            loadChats(); // Update badge
        }
    }
}

function sendMessage() {
    const input = document.getElementById('messageInput');
    const msg = input.value.trim();
    if (msg) {
        const messages = document.getElementById('chatMessages');
        messages.innerHTML += `<div class="chat-message sent mb-4">${msg}</div>`;
        input.value = '';
        messages.scrollTop = messages.scrollHeight; // Auto scroll
        // WebSocket/DB send point
    }
}

function inviteInterview() {
    alert('Mời phỏng vấn đã gửi qua email và chat!');
    // Update status to 'interview'
}

// Analytics Placeholder
function loadAnalytics() {
    // Chart.js integration point
    document.getElementById('viewsChart').innerHTML = '<canvas id="viewsCanvas"></canvas>'; // Ready for chart
    // Similar for others
}

// Premium Modal
function showPremiumOrgModal() {
    document.getElementById('premiumOrgModal').classList.remove('hidden');
}

function closePremiumOrgModal() {
    document.getElementById('premiumOrgModal').classList.add('hidden');
}

// Export Report
function exportReport() {
    alert('Xuất báo cáo thành công! (Tích hợp PDF/CSV export sau)');
}

// Init
loadApplications();
loadChats();
loadOrgProfile();