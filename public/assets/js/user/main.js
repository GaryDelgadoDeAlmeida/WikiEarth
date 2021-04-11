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

if(document.getElementById('filter-by-mineral').length > 0) {
    document.getElementById('filter-by-mineral').addEventListener('change', () => {
        document.getElementById('form--filter-by-mineral').submit();
    });
}