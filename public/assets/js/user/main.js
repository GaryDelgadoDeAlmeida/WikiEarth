if(document.getElementById('category-by-livingThing').length > 0) {
    document.getElementById('category-by-livingThing').addEventListener('change', () => {
        document.getElementById('form--category-by-livingThing').submit();
    });
}

if(document.getElementById('filter-by-livingThing').length > 0) {
    document.getElementById('filter-by-livingThing').addEventListener('change', () => {
        document.getElementById('form--filter-by-livingThing').submit();
    });
}