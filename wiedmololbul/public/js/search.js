const search = document.querySelector('input[placeholder="Search..."]');
const postContainer = document.querySelector(".posts");

function loadPosts(posts) {
    posts.forEach(post => {
        console.log(post);
        createPost(post);
    });
}

function createPost(post) {
    const template = document.querySelector("#post-template");

    const clone = template.content.cloneNode(true);
    const div = clone.querySelector("div");
    div.id = post.id;
    const title = clone.querySelector("h3");
    title.innerHTML = post.title;
    const author = clone.querySelector(".author");
    author.innerHTML = `<strong>By: </strong>${post.author}`;
    const content = clone.querySelector(".content");
    content.innerHTML = post.content;


    postContainer.appendChild(clone);
}

search.addEventListener("keyup", function (event) {
    if (event.key === "Enter") {
        event.preventDefault();

        const data = {search: this.value};

        fetch("/search", {
            method: "POST",
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        }).then(function (response) {
            return response.json();
        }).then(function (posts) {
            postContainer.innerHTML = "";
            loadPosts(posts)
        });
    }
});

