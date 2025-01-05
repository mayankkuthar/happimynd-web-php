<div class="modal fade" id="discountcoupon" tabindex="-1" data-show="true" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog discountcoupon__popup">
    <div class="modal-content">
      <div class="discountcoupon__popup__close" data-dismiss="modal">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
          <path d="M15 9L9 15" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
          <path d="M9 9L15 15" stroke="#9297A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
        </svg>
      </div>
      <div class="discountcoupon__popup__content">
        <h1>Discount Coupon</h1>
        <form class="discountcoupon__popup__content__form" id="coupon_form">
          <div >
            <div class="discountcoupon__popup__form__input">
              <h2>Coupon code</h2>
              {{-- Add error class to input field to show error state like class="error" --}}
              <input type="text" name="couponcode" id="coupon-code" placeholder="Coupon code" required="">
              {{-- Show/Uncommnet below given span tag for error state --}}
              <span class="error" id="coupon-error-msg"></span>
            </div>
          </div>
          <div class="discountcoupon__popup__content__form__btn">
            <button class="discountcoupon__popup__content__form__btn__submit" type="submit" id="coupon_submit">Apply</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>