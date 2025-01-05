<div class="modal fade" id="couponInfo" tabindex="-1" data-show="true" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog commingsoon__popup">
    <div class="modal-content">
      <div class="dashboard__toast " id="toast" style="visibility:hidden">
        <div class="dashboard__toast__text">
          <h1 id="messageInfo"></h1>
        </div>
      </div>
      <div class="sendfullreport__popup__close" data-dismiss="modal">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M15 9L9 15" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M9 9L15 15" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      <diV class="commingsoon__popup__content">
        <p id="info" style="font-size: 22px; font-weight: 500;"></p>
        <input type="text" name="coupon_code" id="coupon_code"
        style="position: absolute; z-index: -999; opacity: 0;">
        <button class="coupon_btn btn btn-primary mb-4" onclick="copyToClipboard();">Copy Coupon Code</button>
        <a id="link" style="width: max-content;" class="" target="_blank">Click here</a>
      </diV>
    </div>
  </div>
</div>