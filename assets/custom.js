
/* attach a submit handler to the form */
$("#searchForm").submit(function(event) {

    /* stop form from submitting normally */
    event.preventDefault();

    var $form = $(this),
        term = $form.find('input[name="keyword"]').val(),
        url = $form.attr('action'),
        button = $form.find('input[name="submit"]');

    /* validation */
    if(term == ''){
        alert("Please Enter Keyword");
        return false;
    }

    /* ajax processing */
    button.attr('disabled', 'disabled');
    button.val('loading...');


    var posting = $.post(url, {
        keyword: term
    });

    posting.done(function(data) {
        button.removeAttr("disabled");
        button.val('Submit');
        output(data);
    });
});

function output(inp) {
    $("#result").empty().append(syntaxHighlight(JSON.stringify(inp, undefined, 4)));
    }

function syntaxHighlight(json) {
    json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
        var cls = 'number';
        if (/^"/.test(match)) {
            if (/:$/.test(match)) {
                cls = 'key';
            } else {
                cls = 'string';
            }
        } else if (/true|false/.test(match)) {
            cls = 'boolean';
        } else if (/null/.test(match)) {
            cls = 'null';
        }
        return '<span class="' + cls + '">' + match + '</span>';
    });
}