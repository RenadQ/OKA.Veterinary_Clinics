  document.addEventListener("DOMContentLoaded", function () {
    // إضافة الجافا سكربت هنا لضبط الحذف والتعديل
    document.querySelectorAll(".delete-btn").forEach(button => {
        button.addEventListener("click", function(event) {
            if (!confirm("Are you sure you want to delete this blog?")) {
                event.preventDefault();
            }
        });
    });
});

// وظيفة تعديل البلوق
function editBlog(id, title, content, category, imageURL) {
	console.log("Editing blog:", id, title, content, category, imageURL);
    // مسح القيم الحالية من الحقول
    document.querySelectorAll(".input-fields input, .input-fields textarea").forEach(input => input.value = "");
    
    // تعيين القيم من البلوق المحدد
    document.getElementById('edit_id').value = id;
    document.querySelector('input[name="title"]').value = title;
    document.querySelector('textarea[name="content"]').value = content;
    document.querySelector('select[name="category"]').value = category;

    
    document.getElementById('imageFileName').value = "Image already uploaded"; 
}