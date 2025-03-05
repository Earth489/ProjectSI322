document.getElementById("file-input").addEventListener("change", function(event) {
    const file = event.target.files[0];  // ดึงไฟล์รูปที่เลือก
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById("preview-image").src = e.target.result;
            document.getElementById("preview-image").style.display = "block"; // ทำให้รูปแสดง
        };
        reader.readAsDataURL(file); // อ่านไฟล์เป็น URL
    
        window.location.href = "main_product_post.php";  // เปลี่ยน "success.php" เป็นหน้าที่คุณต้องการ
    }
        
        
});
