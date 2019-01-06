$('#add-image').click(function(){
    // Recuperation du numero des futurs champs que je vais créer
    const index = +$('#widgets-counter').val();

    // Recuperation du prototype des entrées
    const tmpl = $('#trick_images').data('prototype').replace(/__name__/g, index);

    // Injection du code du prototype au sein de la div
    $('#trick_images').append(tmpl);

    $('#widgets-counter').val(index + 1);

    // Gestion du bouton Supprimer
    handleDeleteButton();
});

function handleDeleteButton() {
    $('button[data-action="delete"]').click(function(){
        const target = this.dataset.target;
        $(target).remove();
    })
}

function updateCounter() {
    const count = +$('#trick_images div.form-group').length;

    $('#widgets-counter').val(count);
}

updateCounter();

handleDeleteButton();