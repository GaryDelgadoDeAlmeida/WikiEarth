if(document.getElementById('category-by-livingThing') != undefined) {
    document.getElementById('category-by-livingThing').addEventListener('change', () => {
        document.getElementById('form--category-by-livingThing').submit();
    });
}

if(document.getElementById('filter-by-livingThing') != undefined) {
    document.getElementById('filter-by-livingThing').addEventListener('change', () => {
        document.getElementById('form--filter-by-livingThing').submit();
    });
}

if(document.getElementById('filter-by-mineral') != undefined) {
    document.getElementById('filter-by-mineral').addEventListener('change', () => {
        document.getElementById('form--filter-by-mineral').submit();
    });
}

if(document.getElementById('filter-by-element') != undefined) {
    document.getElementById('filter-by-element').addEventListener('change', () => {
        document.getElementById('form--filter-by-element').submit();
    });
}

// Fill file input (jquery or bootstrap don't do it)
fillFileUploadLabel("mineral_imgPath");
fillFileUploadLabel("article_mineral_mineral_imgPath");
fillFileUploadLabel("living_thing_imgPath");
fillFileUploadLabel("article_living_thing_livingThing_imgPath");
fillFileUploadLabel("element_imgPath");
fillFileUploadLabel("article_element_element_imgPath");