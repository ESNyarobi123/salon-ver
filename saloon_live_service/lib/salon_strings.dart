/// Salon / service-desk copy for the stylist portal (Flutter).
/// API payloads and models keep backend names (`Order`, `table_number`, etc.).
abstract final class SalonStrings {
  static const appTitle = 'TIPTAP Service desk';
  static const brandSubtitle = 'Service desk — saloon';
  static const splashTagline = 'SERVICE DESK';

  // Tabs (match API order: pending → preparing+ready → served → paid)
  static const tabPending = 'Inasubiri';
  static const tabPreparing = 'Inaendelea / tayari';
  static const tabServed = 'Imeshughulikiwa';
  static const tabPaid = 'Imelipwa';

  static const List<String> bookingTabs = [
    tabPending,
    tabPreparing,
    tabServed,
    tabPaid,
  ];

  static String activeBookingsCount(int n) => 'Booking hai: $n';
  static const menuRefreshData = 'Sasisha data';
  static const menuLogout = 'Toka';
  static const connectionErrorTitle = 'Hitilafu ya muunganisho';
  static const tryAgain = 'Jaribu Tena';

  static const fabNewBooking = 'Booking mpya';
  static const sellProductsCta = 'Uza bidhaa';
  static const menuProductSalesHistory = 'Mauzo ya bidhaa & mapato';
  static const productSalesTitle = 'Mauzo ya bidhaa';
  static const productSalesSubtitle =
      'Haijaonyeshwa kwenye live bookings. Cash au push → malipo kamili.';
  static const newProductSaleTitle = 'Uza bidhaa';
  static const newProductSaleSubtitle =
      'Hatua 2: chagua bidhaa → malipo (cash au USSD).';
  static const stepProductsRetail = 'Bidhaa';
  static const stepPayRetail = 'Malipo';
  static const menuSearchProductsHint = 'Tafuta bidhaa…';
  static const noRetailProducts =
      'Hakuna bidhaa za kuuza. Weka bidhaa chini ya kategoria ya Product kwenye menyu.';
  static const payCashInstant = 'Cash (kamilika mara moja)';
  static const payPushSelcom = 'USSD Push (Selcom)';
  static const pushPhoneLabel = 'Simu ya push *';
  static const completeProductCash = 'Kamilisha (cash)';
  static const continueWithPush = 'Endelea na push';
  static const snackProductSaleRecorded = 'Mauzo ya bidhaa yamesajiliwa ✓';
  static const paymentAppBarProduct = 'Malipo – bidhaa';
  static String productSaleLine(int id, String ref) => 'Uzoaji #$id – $ref';
  static const revenueTodayLabel = 'Mapato leo';
  static const revenue30dLabel = 'Mapato siku 30';
  static const salesCountToday = 'Mauzo leo';
  static const statusAwaitingPush = 'Inasubiri push';
  static const cancelPendingProductTitle = 'Ghairi uzoaji usiolipwa?';
  static const cancelPendingProductBody =
      'Uzoaji huu bado haujalipwa. Unaweza kuughairi na kuuza tena.';
  static const dialogCancelSale = 'Ghhairi';

  static const loginFailed = 'Kuingia kumeshindikana. Jaribu tena.';
  static const loginConnectionError =
      'Hitilafu ya muunganisho. Angalia intaneti na jaribu tena.';

  static const sessionExpired = 'Kipindi kimeisha. Ingia tena.';

  static const apiLoadBookingsFailed = 'Imeshindwa kupakia bookings';
  static const apiCreateBookingFailed = 'Imeshindwa kusajili booking';
  static const apiUpdateBookingFailed = 'Imeshindwa kusasisha booking';
  static const apiDeleteBookingFailed = 'Imeshindwa kufuta booking';
  static const apiLoadProductSalesFailed = 'Imeshindwa kupakia mauzo ya bidhaa';
  static const apiCreateProductSaleFailed = 'Imeshindwa kusajili mauzo ya bidhaa';
  static const apiCancelProductSaleFailed = 'Imeshindwa kughairi uzoaji';

  // Order detail
  static const labelSeat = 'Kiti';
  static const labelClient = 'Mteja';
  static const labelPhone = 'Simu';
  static const sectionServicesProducts = 'Huduma';
  static const labelAppointment = 'Miadi';
  static const totalBill = 'Jumla ya bili';
  static const statusStartService = 'Anza huduma';
  static const statusCompleteService = 'Maliza huduma';
  static const statusConfirmPayment = 'Thibitisha malipo';
  static const paySelcomUssd = 'Lipa kwa Selcom USSD';
  static const deleteBookingTooltip = 'Futa booking';

  // Order card
  static String moreItemsLine(int n) => '+$n vitu zaidi';
  static String linesCount(int n) => n == 1 ? '1 kipengele' : '$n vipengele';

  // New order (3 steps: when → details → services by category)
  static const newBookingTitle = 'Booking mpya';
  static const newBookingSubtitleSteps =
      'Hatua 3: muda → mteja & kiti → huduma kwa kategoria';
  static const stepWhen = 'Muda';
  static const stepDetails = 'Maelezo';
  static const stepServices = 'Huduma';
  static const stepBack = 'Rudi';
  static const stepContinue = 'Endelea';
  static const stepWhenTitle = 'Mteja anataka lini?';
  static const stepWhenHint =
      'Chagua tarehe na saa ya miadi. Unaweza kusahihisha baadaye na manager.';
  static const stepDetailsTitle = 'Kiti na mteja';
  static const labelAppointmentDate = 'Tarehe ya miadi';
  static const labelAppointmentTime = 'Saa';
  static const menuSearchServicesHint = 'Tafuta huduma…';
  static const tapServicesToAdd =
      'Gusa huduma kushoto kuongeza kwenye kikapu.';
  static const chooseServiceToSubmit =
      'Chagua angalau huduma moja ili kuwasilisha.';
  static const noBookableServices =
      'Hakuna huduma za kuweka booking. Ongiza huduma chini ya kategoria ya Service kwenye menyu, kisha sasisha.';
  static const cart = 'Kikapu';
  static const total = 'Jumla';
  static const bookingDetails = 'Maelezo ya booking';
  static const clientNameOptional = 'Jina la mteja (hiari)';
  static const submitBooking = 'Wasilisha booking';
  static const submitting = 'Inawasilisha...';
  static String cartSummary(int count, String amount) =>
      '$count vipengele · TZS $amount';
  static const seatRequired = 'Chagua kiti';
  static const seatHint = 'Mf. Kiti 5';
  static const unknownItem = '—';

  // Payment
  static const paymentAppBar = 'Malipo – booking';
  static const ussdPushTitle = 'Selcom USSD Push';
  static const ussdPushSubtitle = 'Tuma USSD push kwa simu ya mteja';
  static const waitClientUssd =
      'Subiri mteja athibitishe ombi la USSD kwenye simu yake...';
  static const ussdFailedGeneric = 'Imeshindwa kutuma USSD';
  static String paymentPollLine(int remaining) =>
      'Inaangalia kila sekunde 5 ($remaining majaribio yaliyobaki)';
  static const externalPaymentTitle = 'Malipo nje (cash / nyingine)';
  static const externalPaymentHint =
      'Ikiwa mteja amelipa kwa njia nyingine, bonyeza hapa kuthibitisha.';
  static const confirmPaidCash = 'Thibitisha amelipa (cash)';
  static const orderSummaryLines = 'vipengele';
  static String bookingSeatLine(int id, String seat) =>
      'Booking #$id – Kiti $seat';
  static String bookingHeaderLine(int id) => 'Booking #$id';

  // Login & misc UI
  static const brandMark = 'TIPTAP';
  static const loginWelcomeTitle = 'Karibu tena! 👋';
  static const loginWelcomeSubtitle =
      'Ingiza nenosiri lako la service desk ili kuendelea';
  static const labelPasswordField = 'Nenosiri';
  static const passwordFieldHint = 'Ingiza nenosiri lako...';
  static const passwordRequiredValidation = 'Nenosiri linahitajika';
  static const poweredByFooter = 'Powered by TIPTAP Africa 🇹🇿';
  static const labelSeatStar = 'Kiti *';
  static const labelPhoneOptional = 'Simu (hiari)';
  static const hintPhoneShort = '255...';
  static const hintPhoneLong = '255712345678';
  static const menuSearchHintShort = 'Tafuta huduma au bidhaa...';
  static const menuEmptyState = 'Hakuna huduma/bidhaa';
  static const clientNameExampleHint = 'mf. Juma Hassan';

  // Payment screen
  static const paymentTimeoutMessage =
      'Muda umekwisha. Jaribu tena au thibitisha kwa cash.';
  static const confirmCashPaymentTitle = 'Thibitisha malipo?';
  static const confirmCashPaymentBody =
      'Una uhakika mteja amelipa kwa cash au njia nyingine?';
  static const confirmYesPaid = 'Ndiyo, amelipa';
  static const phoneNumberLabel = 'Nambari ya simu *';
  static const phoneNumberHelper = 'Nambari ya Selcom/TTCL ya mteja';
  static const phoneRequiredValidation = 'Nambari ya simu inahitajika';
  static const sendingUssd = 'Inatuma...';
  static const sendUssdPush = 'Tuma USSD Push';
  static const paymentSuccessTitle = 'Malipo yamekamilika! 🎉';
  static const paymentFailedTitle = 'Malipo yameshindwa';
  static const paymentWaitingTitle = 'Inasubiri malipo...';
  static String paymentTxLine(String id) => 'TX: $id';
  static const closeAndReturn = 'Funga & rudi';

  // Dialogs & toasts (service desk)
  static const dialogNo = 'Hapana';
  static const dialogDelete = 'Futa';
  static const dialogLogout = 'Toka';
  static String deleteBookingTitle(int id) => 'Futa booking #$id?';
  static const deleteBookingBody = 'Hatua hii haiwezi kubatilishwa.';
  static const deleteBookingBodyConfirm =
      'Hatua hii haiwezi kubatilishwa. Una uhakika?';
  static const logoutTitle = 'Toka?';
  static const logoutBody = 'Una uhakika unataka kutoka?';
  static String snackBookingDeleted(int id) => 'Booking #$id imefutwa';
  static String snackBookingStatusUpdated(int id, String apiStatus) {
    final label = switch (apiStatus) {
      'pending' => tabPending,
      'preparing' => tabPreparing,
      'served' => tabServed,
      'paid' => tabPaid,
      _ => apiStatus,
    };
    return 'Booking #$id → $label ✓';
  }

  static String headerStylistLine(String name) => 'Stylist: $name';
  static const headerServiceDeskLine = 'Huduma ya salon (desk)';
  static const loginButton = 'Ingia';
  static const newOrderNeedOneItem =
      'Chagua angalau huduma au bidhaa moja';
  static const newOrderCreatedSuccess = 'Booking imesajiliwa! ✅';
}
