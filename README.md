# EduMatch (EDU IP)
Nền tảng thông minh kết nối sinh viên với các cơ hội nghiên cứu và học bổng sau đại học

**EduMatch** là nền tảng thông minh giúp **sinh viên**, **trường đại học**, **giáo sư**, và **nhà cung cấp học bổng** kết nối trực tiếp với nhau.  
Hệ thống áp dụng **AI Matching** để đề xuất học bổng, phòng thí nghiệm nghiên cứu và cơ hội học tập phù hợp nhất cho từng sinh viên.

---

##  1. Sinh viên / Ứng viên

###  Chức năng chính:
- Đăng ký và đăng nhập tài khoản qua email (xác thực OTP).
- Tạo và cập nhật hồ sơ cá nhân:
  - GPA, kỹ năng, thành tích, sở thích nghiên cứu, chủ đề luận án,...
- Tìm kiếm học bổng / chương trình / phòng nghiên cứu theo bộ lọc:
  - Ngành học, quốc gia, GPA, loại học bổng, hình thức học tập,...
- Lưu các học bổng hoặc chương trình yêu thích.
- Nâng cấp gói **Premium** để:
  - Nhận đề xuất AI Matching.
  - Xem **điểm phù hợp (matching score)** giữa hồ sơ và tiêu chí.
- Nộp đơn trực tuyến, đính kèm CV, thư động lực, ấn phẩm,...
- Theo dõi trạng thái hồ sơ:  
  `Đang chờ xử lý`, `Đã duyệt`, `Bị từ chối`, `Mời phỏng vấn`, ...
- Nhận thông báo qua email / hệ thống khi có cập nhật mới.
- Giao tiếp trực tiếp với nhà cung cấp học bổng hoặc giáo sư.

---

##  2. Nhà cung cấp học bổng / Trường đại học / Giáo sư

###  Chức năng chính:
- Tạo và xác minh tài khoản tổ chức qua email hoặc tài liệu chính thức.
- Đăng học bổng / chương trình nghiên cứu kèm thông tin:
  - Tiêu chí ứng tuyển, thời hạn, tài liệu cần thiết,...
- Quản lý danh sách học bổng, chỉnh sửa và cập nhật dễ dàng.
- Đăng ký gói **Premium** để:
  - Nhận danh sách ứng viên phù hợp do AI đề xuất.
  - Xem hồ sơ chi tiết và điểm khớp (matching score).
- Quản lý hồ sơ ứng tuyển: duyệt, từ chối, mời phỏng vấn,...
- Giao tiếp trực tiếp với ứng viên qua hệ thống nhắn tin.
- Bảng thống kê (Dashboard):
  - Số lượt xem, số lượng hồ sơ, tỉ lệ phù hợp,...

---

##  3. Quản trị viên hệ thống

###  Chức năng chính:
- Quản lý mọi tài khoản người dùng (ứng viên, tổ chức, giáo sư,...).
- Kiểm duyệt nội dung học bổng / hồ sơ người dùng.
- Quản lý thanh toán và gói cao cấp.
- Xử lý báo cáo và khiếu nại người dùng.
- Theo dõi lịch sử hoạt động:
  - Cập nhật hồ sơ, nộp đơn, kết quả duyệt, chỉnh sửa nội dung,...

---

##  4. Trình xử lý hệ thống (System Processor)

###  Nhiệm vụ:
- Gửi thông báo và email tự động (nhắc hạn chót, kết quả, cập nhật).
- Xử lý thông báo theo lịch định kỳ.
- Duy trì tốc độ phản hồi nhanh, tối ưu hóa truy vấn dữ liệu.
- Đảm bảo toàn bộ tiến trình làm việc mượt mà và an toàn.

---

##  5. Yêu cầu phi chức năng

| Tiêu chí | Mô tả |
|-----------|--------|
| **Khả năng sử dụng** | Giao diện thân thiện, dễ thao tác. |
| **Độ tin cậy** | Hệ thống ổn định, ít lỗi, đảm bảo dữ liệu chính xác. |
| **Hiệu suất** | Tìm kiếm nhanh, phản hồi tức thời. |
| **Bảo mật** | Dữ liệu và thanh toán được mã hóa, bảo vệ tuyệt đối. |
| **Đa ngôn ngữ** | Hỗ trợ ít nhất Tiếng Việt và Tiếng Anh. |

---

##  6. Kiến trúc và Công nghệ

| Thành phần | Công nghệ sử dụng |
|-------------|------------------|
| **Frontend (Giao diện người dùng)** | HTML, CSS, JavaScript |
| **Backend (Xử lý phía máy chủ)** | Python |
| **Cơ sở dữ liệu (Database)** | MySQL / SQL |
| **AI Matching Engine** | Python (Machine Learning / Recommendation Model) |
| **Realtime Notification / Chat** | JavaScript (WebSocket hoặc Socket.IO) |
| **Triển khai** | Localhost hoặc Cloud Hosting |
| **Công cụ hỗ trợ phát triển** | VS Code, GitHub, XAMPP, Python environment |

### 🔧 Mô hình tổng quan:
- Giao diện web (HTML/CSS/JS) gửi yêu cầu đến backend (Python Flask/Django).
- Backend xử lý logic, kết nối cơ sở dữ liệu (SQL).
- Mô-đun AI (Python) tính điểm phù hợp học bổng.
- Hệ thống gửi kết quả về frontend hiển thị cho sinh viên.

---

##  7. Cấu trúc hệ thống

Hệ thống bao gồm:
1. **Ứng dụng Web** cho sinh viên, tổ chức và quản trị viên.  
2. **Backend Python** để xử lý API, đăng nhập, kết nối SQL và AI Matching.  
3. **Cơ sở dữ liệu SQL** để lưu trữ người dùng, học bổng, đơn ứng tuyển.  
4. **Mô-đun AI Matching** (Python, chạy nền hoặc tích hợp API).

---

##  8. Nhiệm vụ phát triển cho sinh viên

| Gói | Mô tả |
|------|--------|
| **Gói 1** | Phân tích & thiết kế hệ thống (Use case, ERD, SRS, UML,...) |
| **Gói 2** | Phát triển Backend bằng Python |
| **Gói 3** | Phát triển giao diện Web bằng HTML, CSS, JavaScript |
| **Gói 4** | Tích hợp AI Matching & thông báo realtime |
| **Gói 5** | Kiểm thử & triển khai hệ thống |
| **Gói 6** | Soạn tài liệu, báo cáo & slide thuyết trình |

---
