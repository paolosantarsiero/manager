(window.webpackJsonp=window.webpackJsonp||[]).push([["property-view"],{"./src/Template/Layout/js/app/components/property-view/property-view.js":function(e,t,n){"use strict";n.r(t),t.default={components:{RelationView:()=>Promise.all([n.e("vendors/async/flatpickr"),n.e("vendors/async/sleep-promise"),n.e("relation-view")]).then(n.bind(null,"./src/Template/Layout/js/app/components/relation-view/relation-view.js")),ChildrenView:()=>Promise.all([n.e("vendors/async/flatpickr"),n.e("vendors/async/sleep-promise"),n.e("children-view")]).then(n.bind(null,"./src/Template/Layout/js/app/components/children-view/children-view.js"))},props:{tabOpen:{type:Boolean,default:!1},tabOpenAtStart:{type:Boolean,default:!1},isDefaultOpen:{type:Boolean,default:!1}},data(){return{isOpen:this.isDefaultOpen,isLoading:!1,totalObjects:0}},mounted(){this.isOpen=this.tabOpenAtStart},watch:{tabOpen(){this.isOpen=this.tabOpen}},methods:{toggleVisibility(){this.isOpen=!this.isOpen},onToggleLoading(e){this.isLoading=e},onCount(e,t=!1){(0===this.totalObjects||t)&&(this.totalObjects=e)}}}}}]);