(window.webpackJsonp=window.webpackJsonp||[]).push([["trash-index","modules-index"],{"./src/Template/Layout/js/app/pages/modules/index.js":function(e,t,s){"use strict";s.r(t),t.default={props:{ids:{type:String,default:()=>[]}},data:()=>({allIds:[],selectedRows:[]}),created(){try{this.allIds=JSON.parse(this.ids)}catch(e){console.error(e)}},computed:{selectedIds(){return JSON.stringify(this.selectedRows)},allChecked(){return JSON.stringify(this.selectedRows.sort())==JSON.stringify(this.allIds.sort())}},methods:{toggleAll(){this.allChecked?this.unCheckAll():this.checkAll()},checkAll(){this.selectedRows=JSON.parse(JSON.stringify(this.allIds))},unCheckAll(){this.selectedRows=[]},bulkActions(){this.selectedRows.length},exportSelected(){this.selectedRows.length<1||document.getElementById("form-export").submit()},exportAll(){this.unCheckAll(),this.$nextTick(()=>{document.getElementById("form-export").submit()})},trash(){this.selectedRows.length<1||confirm("Move "+this.selectedRows.length+" item to trash")&&document.getElementById("form-delete").submit()},selectRow(e){if("checkbox"!=e.target.type){e.preventDefault();var t=e.target.querySelector("input[type=checkbox]");let s=this.selectedRows.indexOf(t.value);-1!=s?this.selectedRows.splice(s,1):this.selectedRows.push(t.value)}}}}},"./src/Template/Layout/js/app/pages/trash/index.js":function(e,t,s){"use strict";s.r(t);var l=s("./src/Template/Layout/js/app/pages/modules/index.js");t.default={extends:l.default,methods:{restoreItem(){this.selectedRows.length<1||document.getElementById("form-restore").submit()},deleteItem(){this.selectedRows.length<1||confirm("Confirm deletion of "+this.selectedRows.length+" item from the trash")&&document.getElementById("form-delete").submit()}}}}}]);