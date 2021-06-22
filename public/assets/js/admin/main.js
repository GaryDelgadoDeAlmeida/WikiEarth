if(document.getElementById("deleteMedia") != undefined) {
    document.getElementById("deleteMedia").addEventListener("click", async function(e) {
        if(confirm('Are you sure you want to delete this media ?') === true) {
            e.preventDefault();
            let mediaId = await e.target.getAttribute("data-mediaId");

            await deleteMedia(mediaId);
        }
    });
}

if(document.getElementById("deleteArticle") != undefined) {
    document.getElementById("deleteArticle").addEventListener("click", async function(e) {
        if(confirm('Are you sure you want to delete this media ?') === true) {
            e.preventDefault();
            let category = await e.target.getAttribute("data-category");
            let articleId = await e.target.getAttribute("data-articleId");

            await deleteArticle(mediaId);
        }
    });
}

async function deleteMedia(mediaId) {
    if(mediaId != null) {
        await fetch('/admin/media/' + mediaId +'/delete', {
            method: 'DELETE',
        }).then(response => response.json()).then((json) => {
            alert(json.message)
        })
        .catch(error => alert(error));
    }
}

async function deleteArticle(category, articleLivingThingId) {
    if(articleLivingThingId != null && category != null) {
        await fetch('/admin/article/' + category + '/' + articleLivingThingId +'/delete', {
            method: 'DELETE',
        }).then(response => response.json()).then((json) => {
            alert(json.message)
        })
        .catch(error => alert(error));
    }
}

// Fill file input (jquery or bootstrap don't do it)
fillFileUploadLabel("mineral_imgPath");
fillFileUploadLabel("article_mineral_mineral_imgPath");
fillFileUploadLabel("living_thing_imgPath");
fillFileUploadLabel("article_living_thing_livingThing_imgPath");
fillFileUploadLabel("element_imgPath");
fillFileUploadLabel("article_element_element_imgPath");