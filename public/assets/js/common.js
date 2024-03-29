var $collectionHolder, $newLinkLi;

jQuery(document).ready(function() {
    let addButton = '<button type="button" class="add_tag_link btn btn-primary">Add a tag</button>';
    let $collectionHolderGeography = $('ul.geography-tags');
    let $collectionHolderEcology = $('ul.ecology-tags');
    let $collectionHolderBehaviour = $('ul.behaviour-tags');
    let $collectionHolderWayOfLife = $('ul.wayOfLife-tags');
    let $collectionHolderDescription = $('ul.description-tags');
    let $collectionHolderOtherData = $('ul.otherData-tags');
    let $collectionHolderGenerality = $('ul.generality-tags');
    let $collectionHolderCharacteristics = $('ul.characteristics-tags');
    let $collectionHolderProperty = $('ul.properties-tags');
    let $collectionHolderUtilization = $('ul.utilization-tags');
    let $collectionHolderReferences = $('ul.references-tags');
    let $collectionHolderEtymology = $('ul.etymology-tags');
    let $collectionHolderGeology = $('ul.geology-tags');
    let $collectionHolderMining = $('ul.mining-tags');

    let $newLinkLiGeography = $('<li></li>').append($(addButton));
    let $newLinkLiEcology = $('<li></li>').append($(addButton));
    let $newLinkLiBehaviour = $('<li></li>').append($(addButton));
    let $newLinkLiWayOfLife = $('<li></li>').append($(addButton));
    let $newLinkLiDescription = $('<li></li>').append($(addButton));
    let $newLinkLiOtherData = $('<li></li>').append($(addButton));
    let $newLinkLiGenerality = $('<li></li>').append($(addButton));
    let $newLinkLiCharacteristics = $('<li></li>').append($(addButton));
    let $newLinkLiProperty = $('<li></li>').append($(addButton));
    let $newLinkLiUtilization = $('<li></li>').append($(addButton));
    let $newLinkLiReferences = $('<li></li>').append($(addButton));
    let $newLinkLiEtymology = $('<li></li>').append($(addButton));
    let $newLinkLiGeology = $('<li></li>').append($(addButton));
    let $newLinkLiMining = $('<li></li>').append($(addButton));
    
    
    $collectionHolderGeography.find('li').each(function() { addTagFormDeleteLink($(this)); });
    $collectionHolderEcology.find('li').each(function() { addTagFormDeleteLink($(this)); });
    $collectionHolderBehaviour.find('li').each(function() { addTagFormDeleteLink($(this)); });
    $collectionHolderWayOfLife.find('li').each(function() { addTagFormDeleteLink($(this)); });
    $collectionHolderDescription.find('li').each(function() { addTagFormDeleteLink($(this)); });
    $collectionHolderOtherData.find('li').each(function() { addTagFormDeleteLink($(this)); });
    $collectionHolderGenerality.find('li').each(function() { addTagFormDeleteLink($(this)); });
    $collectionHolderCharacteristics.find('li').each(function() { addTagFormDeleteLink($(this)); });
    $collectionHolderProperty.find('li').each(function() { addTagFormDeleteLink($(this)); });
    $collectionHolderUtilization.find('li').each(function() { addTagFormDeleteLink($(this)); });
    $collectionHolderReferences.find('li').each(function() { addTagFormDeleteLink($(this)); });
    $collectionHolderEtymology.find('li').each(function() { addTagFormDeleteLink($(this)); });
    $collectionHolderGeology.find('li').each(function() { addTagFormDeleteLink($(this)); });
    $collectionHolderMining.find('li').each(function() { addTagFormDeleteLink($(this)); });
    
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

    $collectionHolderGenerality.append($newLinkLiGenerality);
    $collectionHolderGenerality.data('index', $collectionHolderGenerality.find('input').length);

    $collectionHolderCharacteristics.append($newLinkLiCharacteristics);
    $collectionHolderCharacteristics.data('index', $collectionHolderCharacteristics.find('input').length);

    $collectionHolderProperty.append($newLinkLiProperty);
    $collectionHolderProperty.data('index', $collectionHolderProperty.find('input').length);

    $collectionHolderUtilization.append($newLinkLiUtilization);
    $collectionHolderUtilization.data('index', $collectionHolderUtilization.find('input').length);
    
    $collectionHolderEtymology.append($newLinkLiEtymology);
    $collectionHolderEtymology.data('index', $collectionHolderEtymology.find('input').length);

    $collectionHolderReferences.append($newLinkLiReferences);
    $collectionHolderReferences.data('index', $collectionHolderReferences.find('input').length);

    $collectionHolderGeology.append($newLinkLiGeology);
    $collectionHolderGeology.data('index', $collectionHolderGeology.find('input').length);

    $collectionHolderMining.append($newLinkLiMining);
    $collectionHolderMining.data('index', $collectionHolderMining.find('input').length);
    
    // When we click on the add button
    $('.add_tag_link').on('click', function(e) {
        let classList = $(this).parent().parent()[0].classList[0];
        
        if(classList == "otherData-tags") {
            $collectionHolder = $collectionHolderOtherData;
            $newLinkLi = $newLinkLiOtherData;
        } else if(classList == "description-tags") {
            $collectionHolder = $collectionHolderDescription;
            $newLinkLi = $newLinkLiDescription;
        } else if(classList == "wayOfLife-tags") {
            $collectionHolder = $collectionHolderWayOfLife;
            $newLinkLi = $newLinkLiWayOfLife;
        } else if(classList == "behaviour-tags") {
            $collectionHolder = $collectionHolderBehaviour;
            $newLinkLi = $newLinkLiBehaviour;
        } else if(classList == "ecology-tags") {
            $collectionHolder = $collectionHolderEcology;
            $newLinkLi = $newLinkLiEcology;
        } else if(classList == "geography-tags") {
            $collectionHolder = $collectionHolderGeography;
            $newLinkLi = $newLinkLiGeography;
        } else if(classList == "generality-tags") {
            $collectionHolder = $collectionHolderGenerality;
            $newLinkLi = $newLinkLiGenerality;
        } else if(classList == "characteristics-tags") {
            $collectionHolder = $collectionHolderCharacteristics;
            $newLinkLi = $newLinkLiCharacteristics;
        } else if(classList == "properties-tags") {
            $collectionHolder = $collectionHolderProperty;
            $newLinkLi = $newLinkLiProperty;
        } else if(classList == "utilization-tags") {
            $collectionHolder = $collectionHolderUtilization;
            $newLinkLi = $newLinkLiUtilization;
        } else if(classList == "references-tags") {
            $collectionHolder = $collectionHolderReferences;
            $newLinkLi = $newLinkLiReferences;
        } else if(classList == "etymology-tags") {
            $collectionHolder = $collectionHolderEtymology;
            $newLinkLi = $newLinkLiEtymology;
        } else if(classList == "geology-tags") {
            $collectionHolder = $collectionHolderGeology;
            $newLinkLi = $newLinkLiGeology;
        } else if(classList == "mining-tags") {
            $collectionHolder = $collectionHolderMining;
            $newLinkLi = $newLinkLiMining;
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

function fillFileUploadLabel(idFileInput) {
    let currentFileuploadInput = document.getElementById(idFileInput);
    if(currentFileuploadInput !== null && currentFileuploadInput !== undefined) {
        currentFileuploadInput.addEventListener("change", function(e) {
            var fileName = e.target.files[0].name;
            let childNodes_1 = this.parentElement.childNodes[1];
            if(childNodes_1 !== null || childNodes_1 !== undefined) {
                if(childNodes_1.id === "fileuploadLabel") {
                    childNodes_1.innerHTML = fileName;
                }
            }
        });
    }
}