<style>
      #loader {
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        position: fixed;
        display: block;
        opacity: 0.7;
        background-color: black;
        z-index: 2000;
        text-align: center;
      }
      #loading-bar-spinner.spinner {
        opacity: 0.7;
        background-color: black;
        z-index: 99;
        left: 50%;
        margin-left: -20px;
        top: 50%;
        margin-top: -20px;
        position: absolute;
        z-index: 19 !important;
        animation: loading-bar-spinner 400ms linear infinite;
      }

      #loading-bar-spinner.spinner .spinner-icon {
        width: 40px;
        height: 40px;
        border:  solid 4px transparent;
        border-top-color:  #00C8B1 !important;
        border-left-color: #00C8B1 !important;
        border-radius: 50%;
      }

      @keyframes loading-bar-spinner {
        0%   { transform: rotate(0deg);   transform: rotate(0deg); }
        100% { transform: rotate(360deg); transform: rotate(360deg); }
      }


    </style>
    <div id="loader" style="display: none">
      <div id="loading-bar-spinner" class="spinner"><div class="spinner-icon"></div></div>
    </div>