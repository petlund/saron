/* global PERSON,
saron
 
 */

"use strict";

function alowedToAddRecords(event, data, tableDef){
    var addButton = $(event.target).find('.jtable-toolbar-item-add-record');
    var userRole = data.serverResponse.user_role;
    
    switch(tableDef.tableName) {
    case saron.table.news.name:
        if(isUserEditorOrOrgEditor(userRole)) 
            addButton.show();
        break;
    case saron.table.people.name:
        if(isUserEditor(userRole)) 
            addButton.show();
        break;
    case saron.table.homes.name:
        if(isUserEditor(userRole)) 
            addButton.show();
        break;
    case saron.table.baptist.name:
        if(isUserEditor(userRole)) 
            addButton.show();
        break;
    case saron.table.member.name:
        if(isUserEditor(userRole)) 
            addButton.show();
        break;
    case saron.table.keys.name:
        if(isUserEditor(userRole)) 
            addButton.show();
        break;
    case saron.table.total.name:
        if(isUserEditor(userRole)) 
            addButton.show();
        break;
    case saron.table.engagement.name:
        if(isUserEditorOrOrgEditor(userRole)) 
            addButton.show();
        break;
    case saron.table.engagements.name:
        if(isUserEditorOrOrgEditor(userRole)) 
            addButton.show();
        break;
    case saron.table.pos.name:
        if(isUserEditorOrOrgEditor(userRole)) 
            addButton.show();
        break;
    case saron.table.role.name:
        if(isUserEditorOrOrgEditor(userRole)) 
            addButton.show();
        break;
    case saron.table.unit.name:
        if(isUserEditorOrOrgEditor(userRole)) 
            addButton.show();
        break;
    case saron.table.unittype.name:
        if(isUserEditorOrOrgEditor(userRole)) 
            addButton.show();
        break;
    case saron.table.role_unittype.name:
        if(isUserEditorOrOrgEditor(userRole)) 
            addButton.show();
    break;
    default:
          console.log("--> " + tableDef.tableName + " is missing in alowedToAddRecords");
    } 
}


function alowedToUpdateOrDelete(event, data, tableDef){
    var editButton = $(data.row).find('.jtable-edit-command-button');
    var deleteButton = $(data.row).find('.jtable-delete-command-button');
    
    var userRole = data.record.user_role;
    
    switch(tableDef.tableName) {
    case saron.table.news.name:
        if(isUserEditorOrOrgEditor(userRole)){ 
            editButton.show();
            deleteButton.show();
        }
        else{
            editButton.hide();
            deleteButton.hide();           
        }
        break;
    case saron.table.people.name:
        if(isUserEditor(userRole)){ 
            editButton.show();
            deleteButton.show();
        }
        else{
            editButton.hide();
            deleteButton.hide()            
        }
        break;
    case saron.table.homes.name:
        if(isUserEditor(userRole)){ 
            editButton.show();
            deleteButton.show();
        }
        else{
            editButton.hide();
            deleteButton.hide();            
        }
        break;
    case saron.table.member.name:
        if(isUserEditor(userRole)){ 
            editButton.show();
            deleteButton.show();
        }
        else{
            editButton.hide();
            deleteButton.hide();           
        }
        break;
    case saron.table.baptist.name:
        if(isUserEditor(userRole)){ 
            editButton.show();
            deleteButton.show();
        }
        else{
            editButton.hide();
            deleteButton.hide();           
        }
        break;
    case saron.table.keys.name:
        if(isUserEditor(userRole)){ 
            editButton.show();
            deleteButton.show();
        }
        else{
            editButton.hide();
            deleteButton.hide()            
        }
        break;
    case saron.table.total.name:
        if(isUserEditor(userRole)){ 
            editButton.show();
            deleteButton.show();
        }
        else{
            editButton.hide();
            deleteButton.hide()            
        }
        break;
    case saron.table.engagement.name:
        if(isUserEditorOrOrgEditor(userRole)){ 
            editButton.show();
            deleteButton.show();
        }
        else{
            editButton.hide();
            deleteButton.hide() ;           
        }
        break;
    case saron.table.engagements.name:
        if(isUserEditorOrOrgEditor(userRole)){ 
            editButton.show();
            deleteButton.show();
        }
        else{
            editButton.hide();
            deleteButton.hide();            
        }
        break;
    case saron.table.pos.name:
        if(isUserEditorOrOrgEditor(userRole)){ 
            editButton.show();
            deleteButton.show();
        }
        else{
            editButton.hide();
            deleteButton.hide();            
        }
        break;
    case saron.table.role.name:
        if(isUserEditorOrOrgEditor(userRole)){ 
            editButton.show();
            deleteButton.show();
        }
        else{
            editButton.hide();
            deleteButton.hide();            
        }
        break;
    case saron.table.unit.name:
        if(isUserEditorOrOrgEditor(userRole)){ 
            editButton.show();
            deleteButton.show();
        }
        else{
            editButton.hide();
            deleteButton.hide();            
        }
        break;
    case saron.table.unittype.name:
        if(isUserEditorOrOrgEditor(userRole)){ 
            editButton.show();
            deleteButton.show();
        }
        else{
            editButton.hide();
            deleteButton.hide();            
        }
        break;
    case saron.table.role_unittype.name:
        if(isUserEditorOrOrgEditor(userRole)){ 
            editButton.show();
            deleteButton.show();
        }
        else{
            editButton.hide();
            deleteButton.hide();            
        }
        break;
      default:
          console.log("--> " + tableDef.tableName + " is missing in alowedToUpdateOrDelete");
    } 
}

function isUserEditorOrOrgEditor(userRole){
    if(userRole === saron.userrole.editor 
            || userRole === saron.userrole.org_editor)
        return true;
    else
        return false;
}

function isUserEditor(userRole){
    if(userRole === saron.userrole.editor) 
        return true;
    else
        return false;
}