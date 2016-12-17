function toggleAll(source) {
    var aInputs = document.getElementsByTagName('input');
    for (var i = 0; i < aInputs.length; i++) {
        if (aInputs[i] != source && aInputs[i].className == source.className) {
            aInputs[i].checked = source.checked;
        }
    }
    if (source.checked) {
        document.getElementById('bulkSubmitBtn').disabled = false;
    } else {
        document.getElementById('bulkSubmitBtn').disabled = true;

    }
}

//Checks if at least one checkbox is selected.
function bulkSubmitBtnManage()
{
    var checkboxs = document.getElementsByClassName("check-all");
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
        document.getElementById('bulkSubmitBtn').disabled = false;
    } else {
        document.getElementById('bulkSubmitBtn').disabled = true;

    }
}

