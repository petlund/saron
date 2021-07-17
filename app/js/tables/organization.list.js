/* global DATE_FORMAT, J_TABLE_ID, PERSON, HOME, PERSON_AND_HOME, OLD_HOME, 
 SARON_URI, SARON_IMAGES_URI, inputFormWidth, inputFormFieldWidth, FullNameOfCongregation, 
 NO_HOME, NEW_HOME_ID,
 POS_ENABLED, POS_DISABLED,
 SUBUNIT_ENABLED, SUBUNIT_DISABLED
 */

"use strict";
$(document).ready(function () {
    const ORG_TREE = "#ORG_LIST";

    $(ORG_TREE).jtable(listTableDef(ORG_TREE, -1, 'Alla')); //-1 => null parent === topnode
    $(ORG_TREE).jtable('load');
    //$(TABLE_ID).find('.jtable-toolbar-item-add-record').hide();
});


function listTableDef(tableId, name, view, selection, keyType, keyValue){
    return {
        title: function (){
            if(name !== null)
                return view + ' "' + name + '" används på följande ställen i organisationsträdet';
            else
                return view + ' används på följande ställen i organisationsträdet';
        },
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'Name', //Set default sorting,
        actions: {
            listAction:   '/' + SARON_URI + 'app/web-api/listOrganizationStructure.php?selection=' + selection + '&' + keyType + '=' + keyValue,
            updateAction:   '/' + SARON_URI + 'app/web-api/updateOrganizationStructure.php?selection=' + selection + '&' + keyType + '=' + keyValue
        },        
        fields: {
            Id: {
                key: true,
                list: false
            },
            PosEnabled: {
                title: '',
                width: '1%',
                sorting: false,
                edit: false,
                create: false,
                delete: false,
                display: function (data) {

                var src = '"/' + SARON_URI + SARON_IMAGES_URI + 'unit.png" title="Underorganisation med ' + data.record.statusSubProposal + ' förslag och ' + data.record.statusSubVacant + ' vakans(er)"';
                        
                var imgTag = _setImageClass(data.record, "Role", src, -1);
                var $imgRole = $(imgTag);

                $imgRole.click(data, function (event){
                    var $tr = $imgRole.closest('tr');
                    $(tableId).jtable('openChildTable', $tr, posTableDef(tableId, data.record.Id, data.record.Name, data.record.OrgUnitType_FK), function(data){
                        data.childTable.jtable('load');
                    });
                });
                return $imgRole;
                }
            },
            Name: {
                width: '15%',
                title: 'Benämning'
                
            },
            UnitPath:{
                edit: false,
                width: '30%',
                title: 'Sökväg'
            },
            SubUnits:{
                edit: false,
                width: '20%',
                title: 'Underenheter'
            },
            UnitType:{
                edit: false,
                width: '20%',
                title: 'Enhetstyp'
            },
            Description:{
                width: '20%',
                title: 'Beskrivning'
            }
        }
    }
}
