$(document).ready(function() {
    // Toggle password visibility
    $('#togglePassword').click(function() {
        const password = $('input[name="password"]');
        const icon = $(this).find('i');
        if (password.attr('type') === 'password') {
            password.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            password.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Load scholarships for student dashboard
    if ($('#scholarships-list').length) {
        loadScholarships();
    }

    // Counter animation
    $('.display-4').each(function() {
        $(this).prop('Counter', 0).animate({
            Counter: $(this).text()
        }, {
            duration: 2000,
            easing: 'swing',
            step: function(now) {
                $(this).text(Math.ceil(now));
            }
        });
    });
});

function loadScholarships() {
    $.ajax({
        url: 'controllers/student_controller.php?action=get_json',
        method: 'GET',
        success: function(data) {
            let html = '';
            data.forEach(function(sch) {
                html += `
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 shadow-lg border-0">
                            <img src="assets/images/scholarship-placeholder.jpg" class="card-img-top" style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h6 class="fw-bold text-primary">${sch.title}</h6>
                                <p class="text-muted small">${sch.description.substring(0, 100)}...</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-success">GPA ${sch.gpa_min}</span>
                                    <span class="badge bg-info">${sch.country}</span>
                                </div>
                                <p class="small text-danger fw-bold mt-2 mb-0">
                                    <i class="fas fa-clock me-1"></i> ${sch.deadline}
                                </p>
                            </div>
                            <div class="card-footer bg-transparent pt-0">
                                <button class="btn btn-primary w-100" onclick="applyScholarship(${sch.id})">
                                    <i class="fas fa-paper-plane me-2"></i> Nộp đơn
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
            $('#scholarships-list').html(html);
        },
        error: function() {
            $('#scholarships-list').html('<div class="col-12"><div class="alert alert-warning">Đang tải học bổng...</div></div>');
        }
    });
}

function applyScholarship(id) {
    if(confirm('Bạn có muốn nộp đơn cho học bổng này?')) {
        $.post('controllers/student_controller.php?action=apply', {scholarship_id: id}, function(data) {
            if(data.success) {
                alert('Nộp đơn thành công!');
                loadScholarships();
            } else {
                alert('Lỗi khi nộp đơn!');
            }
        }, 'json').fail(function() {
            alert('Lỗi kết nối!');
        });
    }
}