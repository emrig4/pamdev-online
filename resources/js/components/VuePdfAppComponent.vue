<template>
  <vue-pdf-app theme="air" :pdf="pdfData" @after-created="createdHandler" @open="openHandler" @pages-rendered="renderedHandler" :config="config">

  	<template #toolbar-right-prepend>
       
          <button onclick="openFullscreen()" class="toolbarButton presentationMode  vue-pdf-app-icon presentation-mode" id="fullscreen-mode"  type="button">
          	<span data-l10n-id="presentation_mode_label">Fullscreen Mode</span>
          </button>
       
    </template>

  </vue-pdf-app>
</template>

<script>
import "../vendor/vue-pdf-app/dist/icons/main.css";
import VuePdfApp from '../vendor/vue-pdf-app'

export default {
  name: "VuePdfAppComponent",
  props: {
    pdfsrc: {
      type: [Object, String],
      required: true,
      default: ''
    },

    base64: {
      type: [Object, String],
      default: ''
    },

    preview_limit: {
    	type: Number,
    	default: 0
    }
  },
  data() {
    return {
      pdfData: '',
      config: {
        toolbar: {
          toolbarViewerRight: {
            openFile: false,
            print: false,
            download: false,
            presentationMode: false
          },
          secondaryToolbar: false,
        },
        secondaryToolbar: {
        	openFile: false,
            print: false,
            download: false,
            presentationMode: false,

            secondaryPresentationMode: false,
            secondaryDownload: false,
            secondaryOpenFile: false,
            secondaryPrint: false,
        }
      }
    }
  },
  components: {
    VuePdfApp: window['vue-pdf-app']
  },

  async mounted(){
  	// this.pdfData = await fetch(this.pdfsrc).then(d=>d.arrayBuffer())
  	// console.log(this.pdfData)


  	this.pdfData = this.base64ToArrayBuffer(this.base64)
  	// console.log(this.base64ToArrayBuffer(this.base64))

  },
  methods: {

  	base64ToArrayBuffer(base64) {
	    var binary_string = window.atob(base64);
	    var len = binary_string.length;
	    var bytes = new Uint8Array(len);
	    for (var i = 0; i < len; i++) {
	        bytes[i] = binary_string.charCodeAt(i);
	    }
	    return bytes.buffer;
	},

	async createdHandler(pdfApp){
		// pdfApp.pdfViewer._pages.slice(0,5)
		// console.log(pdfApp)
		// console.log(pdfApp.pdfViewer)
		console.log(pdfApp)
	},

	async renderedHandler(pdfApp){

		let pdfViewer = await pdfApp.pdfViewer
		let viewer = pdfViewer.viewer
		let pages = pdfViewer._pages
		let page1 = pdfApp.pdfViewer._pages[0]


		if(this.preview_limit){

			// add overlay
	    pdfApp.pdfViewer._pages.forEach((page, i) => {
				if(i >= this.preview_limit   ){
					this.addOverlay(pdfApp, i)
				}
			});


			// return  from page1 - 4 
			pdfApp.pdfViewer._pages = pages.slice(0, (this.preview_limit+4) )


		}else{
			pdfApp.pdfViewer._pages = pages
		}
		

  	// only remove page content and leave a blank canvas
  	// page1Div.querySelectorAll('*').forEach((n) =>{
  	// 	n.remove()
  	// })


	},

    async openHandler(pdfApp) {
   		// window._pdfApp = pdfApp;
    },


    addOverlay(pdfApp, pageNumber){
    	var pageNumber = pageNumber;
		var pdfRect = [0,0,140,150];

		var pageView = pdfApp.pdfViewer.getPageView(pageNumber - 1);
		var screenRect = pageView.viewport.convertToViewportRectangle(pdfRect);

		var x = Math.min(screenRect[0], screenRect[2]), width = Math.abs(screenRect[0] - screenRect[2]);
		var y = Math.min(screenRect[1], screenRect[3]), height = Math.abs(screenRect[1] - screenRect[3]);

		// note: needs to be done in the 'pagerendered' event
		var overlayDiv = document.createElement('div');
		overlayDiv.setAttribute('style', 'z-index: 3000; background-color: whitesmoke;position:absolute;' +
		  'left:' + x + 'px;top:' + '1' + 'px;width:' + '100' + '%;height:' + '100' + '%; display: flex; align-items: center;');
		overlayDiv.innerHTML = '<div class="ereaders-preview-text"><span>END OF PREVIEW</span><p>Subscribe to continue reading and read thousands more..</p><div class="clearfix"></div><a href="/pricings" class="eraders-search-btn" style="padding: 5px 2px; margin: auto">Click here to Subscribe <i class="icon ereaders-right-arrow"></i></a></div>'
		pageView.div.appendChild(overlayDiv);
		
    },
  },
};
</script>
<style type="text/css">
	.pdf-app.air {
	    --pdf-app-background-color: none;
	    --pdf-sidebar-content-color: none;
	     --pdf-toolbar-sidebar-color: none; 
	     --pdf-toolbar-color: white; 
	    --pdf-loading-bar-color: #606c88;
	    --pdf-loading-bar-secondary-color: #11ece5;
	    --pdf-find-results-count-color: #d9d9d9;
	    --pdf-find-results-count-font-color: #525252;
	    --pdf-find-message-font-color: #a6b7d0;
	    --pdf-not-found-color: #f66;
	    --pdf-split-toolbar-button-separator-color: #fff;
	    --pdf-toolbar-font-color: gray;  /* #d9d9d9; */
	    --pdf-button-hover-font-color: #00aff0;
	    --pdf-button-toggled-color: rgb(0 0 0 / 10%);
	    --pdf-horizontal-toolbar-separator-color: #fff;
	    --pdf-input-color: #606c88;
	    --pdf-input-font-color: #d9d9d9;
	    --pdf-find-input-placeholder-font-color: #11ece5;
	    --pdf-thumbnail-selection-ring-color: hsla(0,0%,100%,0.15);
	    --pdf-thumbnail-selection-ring-selected-color: hsla(0,0%,100%,0.3);
	    --pdf-error-wrapper-color: #f55;
	    --pdf-error-more-info-color: #d9d9d9;
	    --pdf-error-more-info-font-color: #000;
	    --pdf-overlay-container-color: rgba(0,0,0,0.2);
	    --pdf-overlay-container-dialog-color: #24364e;
	    --pdf-overlay-container-dialog-font-color: #d9d9d9;
	    --pdf-overlay-container-dialog-separator-color: #fff;
	    --pdf-dialog-button-font-color: #d9d9d9;
	    --pdf-dialog-button-color: #606c88;
	}


	.pdf-app .pdfViewer .page {
	    direction: ltr;
	    width: 816px;
	    height: 1056px;
	    margin: 1px auto 10px auto !important;
	    position: relative;
	    overflow: visible;
	     border: none !important; 
	    background-clip: content-box;
	    background-color: #fff;
	    box-shadow: 0px -2px 5px 0px;
	}

	.pdf-app .toolbarField.pageNumber {
	    -moz-appearance: textfield;
	    min-width: 36px;
	    text-align: right;
	    width: 60px;
	    min-height: 25px;
	    background: none !important;
	}


</style>
