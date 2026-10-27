document.addEventListener("DOMContentLoaded", function () {
    fetch("get_blogs.php")
        .then(response => {
            if (!response.ok) throw new Error("Network error");
            return response.json();
        })
        .then(blogs => {
            console.log("📦 Blogs received:", blogs); // تحقق من البيانات المستلمة
            const blogContainer = document.getElementById("blogContainer");

            if (!blogs || blogs.length === 0) {
                blogContainer.innerHTML = "<p>No blogs available.</p>";
                return;
            }

            blogs.forEach(blog => {
                const blogPost = document.createElement("div");
                blogPost.classList.add("blog-post");

                  blogPost.innerHTML = `
    <a href="post.php?id=${blog.blog_id}" target="_blank" style="display: flex; align-items: center; text-decoration: none; color: inherit;">
        <img src="${blog.image_url}" alt="Blog Image">
        <div class="blog-post-content">
            <h3>${blog.title}</h3>
            <small>Category: ${blog.category}</small>
        </div>
    </a>
`;

                blogContainer.appendChild(blogPost);
            });
        })
        .catch(error => {
            console.error("❌ Error fetching blogs:", error);
            document.getElementById("blogContainer").innerHTML = "<p>Error loading blog posts.</p>";
        });
});