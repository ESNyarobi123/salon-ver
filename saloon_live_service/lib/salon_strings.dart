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

  static const loginFailed = 'Kuingia kumeshindikana. Jaribu tena.';
  static const loginConnectionError =
      'Hitilafu ya muunganisho. Angalia intaneti na jaribu tena.';

  static const sessionExpired = 'Kipindi kimeisha. Ingia tena.';

  static const apiLoadBookingsFailed = 'Imeshindwa kupakia bookings';
  static const apiCreateBookingFailed = 'Imeshindwa kusajili booking';
  static const apiUpdateBookingFailed = 'Imeshindwa kusasisha booking';
  static const apiDeleteBookingFailed = 'Imeshindwa kufuta booking';

  // Order detail
  static const labelSeat = 'Kiti';
  static const labelClient = 'Mteja';
  static const labelPhone = 'Simu';
  static const sectionServicesProducts = 'Huduma & bidhaa';
  static const totalBill = 'Jumla ya bili';
  static const statusStartService = 'Anza huduma';
  static const statusCompleteService = 'Maliza huduma';
  static const statusConfirmPayment = 'Thibitisha malipo';
  static const paySelcomUssd = 'Lipa kwa Selcom USSD';
  static const deleteBookingTooltip = 'Futa booking';

  // Order card
  static String moreItemsLine(int n) => '+$n vitu zaidi';
  static String linesCount(int n) => n == 1 ? '1 kipengele' : '$n vipengele';

  // New order
  static const newBookingTitle = 'Booking mpya';
  static const newBookingSubtitle = 'Chagua huduma / bidhaa';
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
