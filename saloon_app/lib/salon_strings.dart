/// Salon / service-desk copy for the staff Flutter app (`saloon_app`).
/// API payloads, model fields, and backend keys are unchanged.
abstract final class SalonStrings {
  static const appTitle = 'TIPTAP Service desk';
  static const splashTagline = 'SERVICE DESK';
  static const poweredBy = 'Powered by Tiptap Africa';
  static const portalSubtitle = 'Huduma ya salon (service desk)';
  static const brandMark = 'TIPTAP';

  // Bottom navigation
  static const navHome = 'Nyumbani';
  static const navBookings = 'Booking';
  static const navRequests = 'Maombi';
  static const navSalary = 'Mishahara';
  static const navProfile = 'Mimi';

  // Login
  static const signInTitle = 'Ingia';
  static const signInSubtitle = 'Weka taarifa zako kuendelea';
  static const labelEmail = 'Barua pepe';
  static const hintEmail = 'mfano@saloon.com';
  static const labelPassword = 'Nenosiri';
  static const rememberMe = 'Nikumbuke';
  static const signInCta = 'INGIA';

  // Orders / bookings (staff)
  static const screenBookings = 'Booking';
  static const tabNew = 'Mpya';
  static const tabMine = 'Zangu';
  static String newCountBadge(int n) => '$n mpya';
  static const noNewBookings = 'Hakuna booking mpya';
  static const noNewBookingsHint = 'Booking mpya zitaonekana hapa';
  static const noMyBookingsYet = 'Bado huna booking';
  static const noMyBookingsHint = 'Chukua booking kwenye tab ya "Mpya"';
  static String seatLabel(String n) => 'Kiti $n';
  static String moreItems(int n) => '+$n vitu zaidi';
  static String claimToastMessage(int id) => 'Booking #$id imechukuliwa!';
  static const claimToastSubtitle = 'Angalia tab ya "Zangu"';
  static const claimBookingCta = 'CHUKUA BOOKING';
  static const readyToServeBanner = 'TAYARI KUTOA HUDUMA';

  // Status labels (align with service-desk wording)
  static const statusReady = 'Tayari';
  static const statusInProgress = 'Inaendelea';
  static const statusPending = 'Inasubiri';

  // Menu card
  static const menuCardTitle = 'Kadi ya menyu';
  static const menuSearchHint = 'Tafuta chakula... (mf. Savanna)';
  static const menuNoItems = 'Hakuna bidhaa';
  static const menuItemAvailable = 'Inapatikana';
  static const menuItemSoldOut = 'IMEISHA';
  static const menuNotLinkedTitle = 'Haupo kwenye saloon';
  static const menuNotLinkedBody =
      'Lazima uunganishwe na saloon ili uone menyu. Wasiliana na msimamizi wako.';
  static String staffNumberLine(String n) => 'Nambari yako ya huduma: $n';
  /// Shown when staff number is not yet assigned (UI fallback only).
  static const staffNumberUnavailable = '—';

  // Requests
  static const requestsTitle = 'Maombi ya wateja';
  static const requestsTabBills = 'Bili';
  static const requestsTabCalls = 'Simu';
  static const requestsAllCaughtUp = 'Yote yako sawa!';
  static const requestsEmptyHint = 'Hakuna maombi ya wateja kwa sasa';
  static const done = 'Imekamilika';

  /// UI labels for `PendingRequest.type` / table (API values unchanged).
  static String requestTypeLabel(String type) {
    switch (type) {
      case 'request_bill':
        return 'Ombi la bili';
      default:
        return 'Simu ya mhudumu';
    }
  }

  static String requestTableDisplay(String? tableNumber) {
    final t = tableNumber?.trim();
    if (t == null || t.isEmpty) return 'Maeneo ya jumla / mapokezi';
    return seatLabel(t);
  }

  // Payslip
  static const payslipLoadSlipsError = 'Imeshindwa kupakia mishahara';
  static const payslipLoadHistoryError = 'Imeshindwa kupakia historia';
  static const payslipMySalary = 'Mishahara yangu';
  static const payslipSubtitle = 'Payslip na historia ya kazi';
  static const payslipTabSlips = 'Payslip';
  static const payslipTabHistory = 'Historia';
  static const payslipNoSlips = 'Hakuna payslip';
  static const payslipNoSlipsHint =
      'Payslip zitaonekana hapa baada ya mwajiri kuzichakata.';
  static const payslipTotalEarnings = 'Mapato jumla';
  static const payslipNetPay = 'Malipo halisi';
  static const payslipView = 'Angalia';
  static const payslipNoHistory = 'Hakuna historia';
  static const payslipNoHistoryHint =
      'Historia ya uajiri wako itaonekana hapa.';
  static const payslipActive = 'Hai';
  static const payslipEnded = 'Imekwisha';
  static const payslipTemporary = 'Ya muda';
  static const payslipPermanent = 'Ya kudumu';
  static const payslipDetailLoadError = 'Imeshindwa kupakia payslip';
  static const payslipDetailTitle = 'Payslip';
  static const payslipRetry = 'Jaribu tena';
  static String payslipSlipCount(int n) => '$n payslip';
  static const payslipPresent = 'Sasa';
  static const payslipUnknownRestaurant = 'Saloon haijulikani';
  static String payslipLinkedUntil(String date) => 'Hadi $date';
  static const payslipMetaPeriod = 'Kipindi';
  static const payslipMetaRestaurant = 'Saloon';
  static const payslipMetaEmployee = 'Mfanyakazi';
  static const payslipMetaEmployeeId = 'Kitambulisho';
  static const payslipMetaPaidOn = 'Imelipwa tarehe';
  static const payslipSectionEarnings = 'Mapato';
  static const payslipBasicSalary = 'Mshahara wa msingi';
  static const payslipAllowances = 'Posho / allowances';
  static const payslipGrossSalary = 'Jumla kabla ya makato';
  static const payslipSectionDeductions = 'Makato';
  static const payslipPaye = 'PAYE';
  static const payslipNssf = 'NSSF';
  static const payslipTotalDeduction = 'Jumla ya makato';
  static const payslipNetPayHero = 'MALIPO HALISI';
  static const payslipDownloadPdf = 'Pakua PDF';
  static const payslipShare = 'Shiriki';
  static String payslipDownloadingSnack(String period) =>
      'Inapakua payslip ya $period...';

  // Dashboard
  static const dashWaiterBrand = 'TIPTAP SERVICE DESK';
  static const dashNotLinked = 'Hujaunganishwa na saloon yoyote';
  static const dashUnknownLocation = 'Mahali haijulikani';
  static const dashYourCode = 'Nambari yako ya kipekee';
  static const dashShareWithManager = 'Shiriki na msimamizi';
  static const dashShareCodeHint =
      'Mpe msimamizi wa saloon nambari hii ili uunganishwe na kuanza kazi.';
  static const dashOnline = 'Mtandaoni';
  static const dashOffline = 'Nje ya mtandao';
  static const dashGoOnlineTitle = 'Ingia mtandaoni?';
  static const dashGoOfflineTitle = 'Ondoka mtandaoni?';
  static const dashGoOnlineBody =
      'Utapokea simu na booking mpya kutoka kwa wateja.';
  static const dashGoOfflineBody =
      'Hutapokea simu wala booking mpya. Booking zilizo hai hazitatatizwa.';
  static const dashGoOnline = 'Ingia mtandaoni';
  static const dashGoOffline = 'Ondoka mtandaoni';
  static const dashCancel = 'Ghairi';
  static const dashNowOnline = 'Uko mtandaoni!';
  static const dashNowOffline = 'Uko nje ya mtandao';
  static const dashNowOnlineSub = 'Tayari kwa booking na simu';
  static const dashNowOfflineSub = 'Hutapokea booking mpya';
  static const dashStatusUpdateFail = 'Imeshindwa kusasisha hali';
  static const dashStatusUpdateFailSub =
      'Angalia muunganisho na jaribu tena.';
  static const dashQrTitle = 'QR ya huduma (service desk)';
  static const dashClose = 'Funga';
  static const dashStatRequests = 'Maombi';
  static const dashStatActive = 'Hai';
  static const dashStatReady = 'Tayari';
  static const dashUrgent = 'Ya haraka';
  static const dashNewBookingsSection = 'Booking mpya';
  static const dashNoNewBookings = 'Hakuna booking mpya';
  static String dashBookingLine(int id) => 'Booking #$id';
  static const dashClaim = 'CHUKUA';
  static const dashMotivationSection = 'Sifa (motisha)';

  // Register (headlines only; validators can stay bilingual where needed)
  static const registerHeadline = 'Fungua akaunti ya huduma (salon)';
  static const registerJoin = 'JIUNGE';
  static const registerSuccessTitle = 'Hongera! 🎉';
  static const registerSuccessBodyDefault = 'Akaunti yako imeundwa!';
  static const registerUniqueNumber = 'Nambari yako ya kipekee';
  static const registerGotIt = 'Sawa';
  static const registerStepPersonal = 'Taarifa';
  static const registerStepSecurity = 'Usalama';
  static const registerPersonalTitle = 'Taarifa binafsi';
  static const registerPersonalSubtitle = 'Tuambie kiasi kuhusu wewe';
  static const labelFirstName = 'Jina la kwanza';
  static const hintFirstName = 'mf. Amina';
  static const valEnterFirstName = 'Ingiza jina la kwanza';
  static const labelLastName = 'Jina la mwisho';
  static const hintLastName = 'mf. Juma';
  static const valEnterLastName = 'Ingiza jina la mwisho';
  static const labelEmailRegister = 'Barua pepe';
  static const hintEmailRegister = 'mfano@saloon.com';
  static const valEnterEmail = 'Ingiza barua pepe';
  static const valInvalidEmail = 'Barua pepe si sahihi';
  static const labelPhone = 'Nambari ya simu';
  static const hintPhone = '255789123456';
  static const valEnterPhone = 'Ingiza nambari ya simu';
  static const valInvalidPhone = 'Nambari ya simu si sahihi';
  static const labelLocationOptional = 'Mahali (si lazima)';
  static const hintLocation = 'Dar es Salaam';
  static const registerSecurityTitle = 'Usalama';
  static const registerSecuritySubtitle = 'Unda nenosiri thabiti';
  static const labelConfirmPassword = 'Rudia nenosiri';
  static const valEnterPassword = 'Ingiza nenosiri';
  static const valPasswordMin8 = 'Nenosiri lazima liwe na angalau herufi 8';
  static const valConfirmPassword = 'Rudia nenosiri';
  static const valPasswordsNoMatch = 'Manenosiri hayalingani';
  static const registerCreateAccount = 'UNDA AKAUNTI';
  static const registerBack = 'Rudi';
  static const registerNext = 'ENDELEA';
  static const registerAlreadyHave = 'Tayari una akaunti? ';
  static const registerSignInLink = 'Ingia';

  // Me / profile hub
  static const meProfileTitle = 'Wasifu wangu';
  static const meActive = 'Hai';
  static const meNotLinkedTitle = 'Hujaunganishwa na saloon';
  static const meNotLinkedBody =
      'Shiriki nambari hii na msimamizi wa saloon ili uunganishwe na kupokea booking.';
  static const meQrSectionTitle = 'Msimbo wako wa QR';
  static const meQrSectionSubtitle =
      'Onyesha mteja QR ili apatie tips / maelekezo.';
  static const meShareQrMessagePrefix = 'Skani uweze kuagiza nami!\n';
  static String meWaiterCodeLine(String code) => 'MSIMBO: $code';
  static String meReviewCount(int n) => '$n maoni';
  static const meShareQrSubject = 'TIPTAP — QR ya huduma';
  static const meShareQrLink = 'Shiriki kiungo cha QR';
  static const meTotalTips = 'Jumla ya tips';
  static const meTipsGiven = 'Tips zilizopokelewa';
  static const meAvgRating = 'Wastani wa ukadiriaji';
  static const meEarningsSection = 'Mapato';
  static const meTipsTotalSubtitle = 'Jumla ya tips zako';
  static const meCustomerReviews = 'Maoni ya wateja';
  static const meNoReviewsYet = 'Bado hakuna maoni';
  static const meNoEmail = 'Hakuna barua pepe';
  static const meWorkplace = 'Mahali pa kazi';
  static const meNotifications = 'Arifa';
  static const meNotificationsSub = 'Booking na tips';
  static const meAbout = 'Kuhusu TIPTAP';
  static const meAboutSub = 'Toleo 1.0.0';
  static const meLogout = 'Toka';
  static const meSignOut = 'Ondoka';
  static const meDefaultRestaurant = 'Saloon';

  // Profile (simple screen)
  static const profileScreenTitle = 'Wasifu wangu';
  static const profileLabelName = 'Jina';
  static const profileLabelEmail = 'Barua pepe';
  static const profileLabelRestaurant = 'Saloon';
  static const profileLabelStaffCode = 'Nambari ya huduma';
  static const profileShareQrTitle = 'Shiriki QR yako';
  static const profileShareQrLink = 'Shiriki kiungo cha QR';

  // Incoming request / call overlay
  static const callBillRequest = 'Ombi la bili';
  static const callCustomerCalling = 'Mteja anapiga';
  static const callIncoming = 'Simu inayoingia';
  static const callJustNow = 'Hivi sasa';
  static String callMinutesAgo(int m) => '$m dakika zilizopita';
  static String callQueueWaiting(int count) =>
      '+$count zingine zinasubiri kwenye foleni';
  static const callDecline = 'Kataa';
  static const callAccept = 'Kubali';
  static const notifIncomingTicker = 'Simu inayoingia';
  static String notifCallBodyLine(String tableLine) =>
      '$tableLine — Gusa kufungua';

  /// Android notification channel labels (visible in system settings).
  static const notifIncomingChannelName =
      'Simu zinazoingia / Incoming calls';
  static const notifIncomingChannelDesc =
      'Arifa za simu za wateja / Customer call alerts';
  static const notifForegroundChannelName =
      'Huduma nyuma / Background service';

  /// Persistent foreground service (Android) while listening for requests.
  static const foregroundServiceTitle = 'TIPTAP — Huduma ya nyuma';
  static const foregroundServiceBody =
      'Inasikiliza simu za wateja… / Listening for customer calls…';

  // Ratings / tips standalone screens
  static const ratingsScreenTitle = 'Ukadiriaji wangu';
  static const ratingsEmpty = 'Bado hakuna ukadiriaji';
  static const tipsScreenTitle = 'Tips zangu';
  static const tipsTotalEarned = 'Jumla ya tips ulizopokea';
  static const tipsRecentHistory = 'Historia ya karibuni';
  static const tipsEmpty = 'Bado hakuna historia ya tips';
  static String tipsBookingRef(int? id) =>
      id == null ? '' : 'Booking #$id';
}
