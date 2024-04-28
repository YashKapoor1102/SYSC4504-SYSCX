/**
 * Sets the text content of the posts along with the 
 * time it was posted.
 */
document.addEventListener('DOMContentLoaded', function() {
    let postsContainer = document.getElementById('posts');

    posts.forEach(function(post) {
       let details = document.createElement('details');
       let summary = document.createElement('summary');
       summary.textContent = "Posted on " + post.post_date;
       let p = document.createElement('p');
       p.textContent = post.post;

       details.appendChild(summary);
       details.appendChild(p);

       postsContainer.appendChild(details);
    });
 });