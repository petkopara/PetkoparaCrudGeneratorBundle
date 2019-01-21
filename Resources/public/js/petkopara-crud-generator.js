function toggleAll(source) {
    var form = source.closest('form');
    var aInputs = form.querySelectorAll('input');
    for (var i = 0; i < aInputs.length; i++) {
        if (aInputs[i] != source && aInputs[i].className == source.className) {
            aInputs[i].checked = source.checked;
        }
    }
    if (source.checked) {
        form.querySelector('input[type=submit]').disabled = false;
    } else {
        form.querySelector('input[type=submit]').disabled = true;
    }
}

//Checks if at least one checkbox is selected.
function bulkSubmitBtnManage(source)
{
    var form = source.closest('form');
    var checkboxs = form.querySelectorAll(".check-all");
    var selected = false;
    for (var i = 0, l = checkboxs.length; i < l; i++)
    {
        if (checkboxs[i].checked)
        {
            selected = true;
            break;
        }
    }
    if (selected) {
        form.querySelector('input[type=submit]').disabled = false;
    } else {
        form.querySelector('input[type=submit]').disabled = true;
    }
}
