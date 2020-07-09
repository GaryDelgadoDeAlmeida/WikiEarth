var $collectionHolder, $newLinkLi;
// var $addTagButton = $('<button type="button" class="add_tag_link">Add a tag</button>');
// var $newLinkLi = $('<li></li>').append($addTagButton);

jQuery(document).ready(function() {
    let $collectionHolderGeography = $('ul.geography-tags');
    let $collectionHolderEcology = $('ul.ecology-tags');
    let $collectionHolderBehaviour = $('ul.behaviour-tags');
    let $collectionHolderWayOfLife = $('ul.wayOfLife-tags');
    let $collectionHolderDescription = $('ul.description-tags');
    let $collectionHolderOtherData = $('ul.otherData-tags');

    let $newLinkLiGeography = $('<li></li>').append($('<button type="button" class="add_tag_link btn btn-primary">Add a tag</button>'));
    let $newLinkLiEcology = $('<li></li>').append($('<button type="button" class="add_tag_link btn btn-primary">Add a tag</button>'));
    let $newLinkLiBehaviour = $('<li></li>').append($('<button type="button" class="add_tag_link btn btn-primary">Add a tag</button>'));
    let $newLinkLiWayOfLife = $('<li></li>').append($('<button type="button" class="add_tag_link btn btn-primary">Add a tag</button>'));
    let $newLinkLiDescription = $('<li></li>').append($('<button type="button" class="add_tag_link btn btn-primary">Add a tag</button>'));
    let $newLinkLiOtherData = $('<li></li>').append($('<button type="button" class="add_tag_link btn btn-primary">Add a tag</button>'));
    
    
    $collectionHolderGeography.find('li').each(function() { addTagFormDeleteLink($(this)); });
    $collectionHolderEcology.find('li').each(function() { addTagFormDeleteLink($(this)); });
    $collectionHolderBehaviour.find('li').each(function() { addTagFormDeleteLink($(this)); });
    $collectionHolderWayOfLife.find('li').each(function() { addTagFormDeleteLink($(this)); });
    $collectionHolderDescription.find('li').each(function() { addTagFormDeleteLink($(this)); });
    $collectionHolderOtherData.find('li').each(function() { addTagFormDeleteLink($(this)); });
    
    $collectionHolderGeography.append($newLinkLiGeography);
    $collectionHolderGeography.data('index', $collectionHolderGeography.find('input').length);

    $collectionHolderEcology.append($newLinkLiEcology);
    $collectionHolderEcology.data('index', $collectionHolderEcology.find('input').length);

    $collectionHolderBehaviour.append($newLinkLiBehaviour);
    $collectionHolderBehaviour.data('index', $collectionHolderBehaviour.find('input').length);

    $collectionHolderWayOfLife.append($newLinkLiWayOfLife);
    $collectionHolderWayOfLife.data('index', $collectionHolderWayOfLife.find('input').length);

    $collectionHolderDescription.append($newLinkLiDescription);
    $collectionHolderDescription.data('index', $collectionHolderDescription.find('input').length);

    $collectionHolderOtherData.append($newLinkLiOtherData);
    $collectionHolderOtherData.data('index', $collectionHolderOtherData.find('input').length);
    
    // When we click on the add button
    $('.add_tag_link').on('click', function(e) {
        if($(this).parent().parent()[0].classList[0] == "otherData-tags") {
            $collectionHolder = $collectionHolderOtherData;
            $newLinkLi = $newLinkLiOtherData;
        } else if($(this).parent().parent()[0].classList[0] == "description-tags") {
            $collectionHolder = $collectionHolderDescription;
            $newLinkLi = $newLinkLiDescription;
        } else if($(this).parent().parent()[0].classList[0] == "wayOfLife-tags") {
            $collectionHolder = $collectionHolderWayOfLife;
            $newLinkLi = $newLinkLiWayOfLife;
        } else if($(this).parent().parent()[0].classList[0] == "behaviour-tags") {
            $collectionHolder = $collectionHolderBehaviour;
            $newLinkLi = $newLinkLiBehaviour;
        } else if($(this).parent().parent()[0].classList[0] == "ecology-tags") {
            $collectionHolder = $collectionHolderEcology;
            $newLinkLi = $newLinkLiEcology;
        } else if($(this).parent().parent()[0].classList[0] == "geography-tags") {
            $collectionHolder = $collectionHolderGeography;
            $newLinkLi = $newLinkLiGeography;
        }

        if($collectionHolder !== undefined) {
            addTagForm($collectionHolder, $newLinkLi);
        }
    });
});

function addTagForm($collectionHolder, $newLinkLi) {
    var prototype = $collectionHolder.data('prototype');
    var index = $collectionHolder.data('index');
    var newForm = prototype;

    newForm = newForm.replace(/__name__/g, index);
    $collectionHolder.data('index', index + 1);
    
    var $newFormLi = $('<li></li>').append(newForm);
    $newLinkLi.before($newFormLi);

    addTagFormDeleteLink($newFormLi);
}

function addTagFormDeleteLink($tagFormLi) {
    var $removeFormButton = $('<button type="button" class="btn btn-danger">Delete this tag</button>');
    $tagFormLi.append($removeFormButton);

    // When we click on the delete button
    $removeFormButton.on('click', function(e) {
        $tagFormLi.remove();
    });
}