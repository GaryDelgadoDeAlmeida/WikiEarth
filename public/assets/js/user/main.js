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