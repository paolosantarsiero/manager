(window.webpackJsonp=window.webpackJsonp||[]).push([["richtext-editor-input"],{"./src/Template/Layout/js/app/components/richtext-editor-input.js":function(e,t,a){"use strict";a.r(t);var n=a("./src/Template/Layout/js/config/config.js");t.default={template:"\n    <div>\n        <slot></slot>\n    </div>\n    ",props:{el:{type:HTMLTextAreaElement}},async mounted(){if("undefined"==typeof CKEDITOR){var e=document.createElement("script");e.type="text/javascript",e.src="https://cdn.ckeditor.com/4.11.3/standard/ckeditor.js",document.getElementsByTagName("head")[0].appendChild(e)}const t=this.el,a=t.getAttribute("ckconfig");let s={};n.b&&(s=n.b[a]),this.setupCKEDITOR(t,s)},methods:{setupCKEDITOR(e,t){setTimeout(()=>{if("undefined"==typeof CKEDITOR)this.setupCKEDITOR(e,t);else{let a=CKEDITOR.replace(e,t);e.dataset.originalValue=e.value,a.on("change",()=>{e.value=a.getData();let t=e.value!==e.dataset.originalValue;e.dispatchEvent(new CustomEvent("change",{bubbles:!0,detail:{id:e.id,isChanged:t}}))})}},25)}}}}}]);