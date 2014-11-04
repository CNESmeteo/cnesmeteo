var dropdown_elem_id = "Form-form-field-User-organization_id";
var dropdown_elem = $("#" + dropdown_elem_id);

dropdown_elem.on('change', function(e){
    dropdown_elem.request('onOrganizationChange', {
        data: { value: dropdown_elem.val()
        }
    });
});
console.log('js executed!!', dropdown_elem);