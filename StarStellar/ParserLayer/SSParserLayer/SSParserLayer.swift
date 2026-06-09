//
//  SSParserLayer.swift
//  StarStellar
//
//  Created by Apple on 18/07/19.
//  Copyright © 2019 Apple. All rights reserved.
//

import UIKit


typealias blockStatusMessage = (String?, String?) -> Void
typealias blockArrayWithMessage = (String?, String?, [Any]?) -> Void
typealias blockDictWithMessage = (String?, String?, [AnyHashable : Any]?) -> Void

func APP_SERVICE(strService: String?) -> String? {
    return "\(StringConstant.Url.baseURL)\(strService ?? "")"
}
func APP_SERVICE_DEV(strService: String?) -> String? {
    return "\(StringConstant.Url.devURL)\(strService ?? "")"
}

class SSParserLayer: NSObject {   

    class func callGenerateOTPForEngineerAndTELogin(_ dictParam: [AnyHashable : Any]?, handler completeHandler: @escaping blockDictWithMessage) {
        BaseJSONParser.baseService(withPostData: APP_SERVICE(strService: "ws_generate_otp_for_engineer_and_te_login.php?"), withParam: dictParam, success: { dictParser in
            completeHandler(dictParser?["process_status"] as? String, dictParser?["process_message"] as? String, dictParser)
        }, failed: { strErrorMsg in
            completeHandler("failed", strErrorMsg, nil)
        })
    }
    
    class func callLoginWithOTPForEngineerAndTE(_ dictParam: [AnyHashable : Any]?, handler completeHandler: @escaping blockDictWithMessage) {
        BaseJSONParser.baseService(withPostData: APP_SERVICE(strService: "ws_login_with_otp_for_engineer_and_te.php?"), withParam: dictParam, success: { dictParser in
            completeHandler(dictParser?["process_status"] as? String, dictParser?["process_message"] as? String, dictParser)
        }, failed: { strErrorMsg in
            completeHandler("failed", strErrorMsg, nil)
        })
    }
    
    //MARK: - Engineer
    
    //Check TE exists by TECODE
    class func callCheckTEExistByTECode(_ dictParam: [AnyHashable : Any]?, handler completeHandler: @escaping blockDictWithMessage) {
        BaseJSONParser.baseService(withPostData: APP_SERVICE(strService: "ws_check_te_code_for_engineer.php?"), withParam: dictParam, success: { dictParser in
            completeHandler(dictParser?["process_status"] as? String, dictParser?["process_message"] as? String, dictParser)
        }, failed: { strErrorMsg in
            completeHandler("failed", strErrorMsg, nil)
        })
    }
    
    //Generate OTP for Engineer login
    class func callGenerateOTPForEngineerLogin(_ dictParam: [AnyHashable : Any]?, handler completeHandler: @escaping blockDictWithMessage) {
        BaseJSONParser.baseService(withPostData: APP_SERVICE(strService: "ws_generate_otp_for_engineer_login.php?"), withParam: dictParam, success: { dictParser in
            completeHandler(dictParser?["process_status"] as? String, dictParser?["process_message"] as? String, dictParser)
        }, failed: { strErrorMsg in
            completeHandler("failed", strErrorMsg, nil)
        })
    }
    
    //Engineer Login (PART OF SIGN UP)
    class func callEngineerLogin(_ dictParam: [AnyHashable : Any]?, handler completeHandler: @escaping blockDictWithMessage) {
        BaseJSONParser.baseService(withPostData: APP_SERVICE(strService: "ws_engineer_login_with_otp.php?"), withParam: dictParam, success: { dictParser in
            completeHandler(dictParser?["process_status"] as? String, dictParser?["process_message"] as? String, dictParser)
        }, failed: { strErrorMsg in
            completeHandler("failed", strErrorMsg, nil)
        })
    }
    
    class func callContactPersonCategoryForEngineer(_ dictParam: [AnyHashable : Any]?, handler completeHandler: @escaping blockDictWithMessage) {
        BaseJSONParser.baseService(withPostData: APP_SERVICE(strService: "ws_show_contact_person_categories_for_engineer.php?"), withParam: dictParam, success: { dictParser in
            completeHandler(dictParser?["process_status"] as? String, dictParser?["process_message"] as? String, dictParser)
        }, failed: { strErrorMsg in
            completeHandler("failed", strErrorMsg, nil)
        })
    }
    
    //Show Pending Site Recomendation
    class func callShowPendingSiteRecommendation(_ dictParam: [AnyHashable : Any]?, handler completeHandler: @escaping blockDictWithMessage) {
        BaseJSONParser.baseService(withPostData: APP_SERVICE(strService: "ws_show_pending_recommendation_for_engineer.php?"), withParam: dictParam, success: { dictParser in
            completeHandler(dictParser?["process_status"] as? String, dictParser?["process_message"] as? String, dictParser)
        }, failed: { strErrorMsg in
            completeHandler("failed", strErrorMsg, nil)
        })
    }
    
    //Show Approved Site Recomendation
    class func callShowApprovedSiteRecommendation(_ dictParam: [AnyHashable : Any]?, handler completeHandler: @escaping blockDictWithMessage) {
        BaseJSONParser.baseService(withPostData: APP_SERVICE(strService: "ws_show_approved_recommendation_for_engineer.php?"), withParam: dictParam, success: { dictParser in
            completeHandler(dictParser?["process_status"] as? String, dictParser?["process_message"] as? String, dictParser)
        }, failed: { strErrorMsg in
            completeHandler("failed", strErrorMsg, nil)
        })
    }
    
    //Show Rejected Site Recomendation
    class func callShowRejectedSiteRecommendation(_ dictParam: [AnyHashable : Any]?, handler completeHandler: @escaping blockDictWithMessage) {
        BaseJSONParser.baseService(withPostData: APP_SERVICE(strService: "ws_show_rejected_recommendation_for_engineer.php?"), withParam: dictParam, success: { dictParser in
            completeHandler(dictParser?["process_status"] as? String, dictParser?["process_message"] as? String, dictParser)
        }, failed: { strErrorMsg in
            completeHandler("failed", strErrorMsg, nil)
        })
    }
    
    //Show Profile For Engineer
    class func callShowProfileForEngineer(_ dictParam: [AnyHashable : Any]?, handler completeHandler: @escaping blockDictWithMessage) {
        BaseJSONParser.baseService(withPostData: APP_SERVICE(strService: "ws_profile_details_for_engineer.php?"), withParam: dictParam, success: { dictParser in
            completeHandler(dictParser?["process_status"] as? String, dictParser?["process_message"] as? String, dictParser)
        }, failed: { strErrorMsg in
            completeHandler("failed", strErrorMsg, nil)
        })
    }
    
    //Homescreen contents Engineer
    class func callHomescreenContentEngineer(_ dictParam: [AnyHashable : Any]?, handler completeHandler: @escaping blockDictWithMessage) {
        BaseJSONParser.baseService(withPostData: APP_SERVICE(strService: "ws_home_screen_details_for_engineer.php?"), withParam: dictParam, success: { dictParser in
            completeHandler(dictParser?["process_status"] as? String, dictParser?["process_message"] as? String, dictParser)
        }, failed: { strErrorMsg in
            completeHandler("failed", strErrorMsg, nil)
        })
    }
    
    //Show My Gifts
    class func callShowMyGifts(_ dictParam: [AnyHashable : Any]?, handler completeHandler: @escaping blockDictWithMessage) {
        BaseJSONParser.baseService(withPostData: APP_SERVICE(strService: "ws_show_my_gift.php?"), withParam: dictParam, success: { dictParser in
            completeHandler(dictParser?["process_status"] as? String, dictParser?["process_message"] as? String, dictParser)
        }, failed: { strErrorMsg in
            completeHandler("failed", strErrorMsg, nil)
        })
    }
    
    //Show My Gifts
    class func callShowMyGiftsCategory(_ dictParam: [AnyHashable : Any]?, handler completeHandler: @escaping blockDictWithMessage) {
        BaseJSONParser.baseService(withPostData: APP_SERVICE(strService: "category-list.php?"), withParam: dictParam, success: { dictParser in
            completeHandler(dictParser?["process_status"] as? String, dictParser?["process_message"] as? String, dictParser)
        }, failed: { strErrorMsg in
            completeHandler("failed", strErrorMsg, nil)
        })
    }
    
    //Make gift order
    class func callMakeGiftOrder(_ dictParam: [AnyHashable : Any]?, handler completeHandler: @escaping blockDictWithMessage) {
        BaseJSONParser.baseService(withPostData: APP_SERVICE(strService: "ws_make_gift_order.php?"), withParam: dictParam, success: { dictParser in
            completeHandler(dictParser?["process_status"] as? String, dictParser?["process_message"] as? String, dictParser)
        }, failed: { strErrorMsg in
            completeHandler("failed", strErrorMsg, nil)
        })
    }
    
    //Show My pending order
    class func callShowMyPendingOrder(_ dictParam: [AnyHashable : Any]?, handler completeHandler: @escaping blockDictWithMessage) {
        BaseJSONParser.baseService(withPostData: APP_SERVICE(strService: "ws_show_my_pending_order_history.php?"), withParam: dictParam, success: { dictParser in
            completeHandler(dictParser?["process_status"] as? String, dictParser?["process_message"] as? String, dictParser)
        }, failed: { strErrorMsg in
            completeHandler("failed", strErrorMsg, nil)
        })
    }
    
    //Show My Delivered order
    class func callShowMyDeliveredOrder(_ dictParam: [AnyHashable : Any]?, handler completeHandler: @escaping blockDictWithMessage) {
        BaseJSONParser.baseService(withPostData: APP_SERVICE(strService: "ws_show_my_delivered_order_history_V2.php?"), withParam: dictParam, success: { dictParser in
            completeHandler(dictParser?["process_status"] as? String, dictParser?["process_message"] as? String, dictParser)
        }, failed: { strErrorMsg in
            completeHandler("failed", strErrorMsg, nil)
        })
    }
    
    //Submit Support with respected order
    class func callSubmitSupportWithRespectedOrder(_ dictParam: [AnyHashable : Any]?, handler completeHandler: @escaping blockDictWithMessage) {
        BaseJSONParser.baseService(withPostData: APP_SERVICE(strService: "ws_submit_support_with_respect_to_order.php?"), withParam: dictParam, success: { dictParser in
            completeHandler(dictParser?["process_status"] as? String, dictParser?["process_message"] as? String, dictParser)
        }, failed: { strErrorMsg in
            completeHandler("failed", strErrorMsg, nil)
        })
    }
    
    //Show My Ledger
    class func callShowMyLedger(_ dictParam: [AnyHashable : Any]?, handler completeHandler: @escaping blockDictWithMessage) {
        BaseJSONParser.baseService(withPostData: APP_SERVICE(strService: "ws_show_my_ledger_for_engineer.php?"), withParam: dictParam, success: { dictParser in
            completeHandler(dictParser?["process_status"] as? String, dictParser?["process_message"] as? String, dictParser)
        }, failed: { strErrorMsg in
            completeHandler("failed", strErrorMsg, nil)
        })
    }
    
    //Show notification for Engineer
    class func callNotificationForEngineer(_ dictParam: [AnyHashable : Any]?, handler completeHandler: @escaping blockDictWithMessage) {
        BaseJSONParser.baseService(withPostData: APP_SERVICE(strService: "show_notifications_for_engineer.php?"), withParam: dictParam, success: { dictParser in
            completeHandler(dictParser?["process_status"] as? String, dictParser?["process_message"] as? String, dictParser)
        }, failed: { strErrorMsg in
            completeHandler("failed", strErrorMsg, nil)
        })
    }
    
    //Show notification for Engineer
    class func callExpectedProduct(_ dictParam: [AnyHashable : Any]?, handler completeHandler: @escaping blockDictWithMessage) {
        BaseJSONParser.baseService(withPostData: APP_SERVICE(strService: "show_product_list.php?"), withParam: dictParam, success: { dictParser in
            completeHandler(dictParser?["process_status"] as? String, dictParser?["process_message"] as? String, dictParser)
        }, failed: { strErrorMsg in
            completeHandler("failed", strErrorMsg, nil)
        })
    }
    
    //Confirm Gift Received
    class func callGiftReceived(_ dictParam: [AnyHashable : Any]?, handler completeHandler: @escaping blockDictWithMessage) {
        BaseJSONParser.baseService(withPostData: APP_SERVICE(strService: "ws_confirm_order_received.php?"), withParam: dictParam, success: { dictParser in
            completeHandler(dictParser?["process_status"] as? String, dictParser?["process_message"] as? String, dictParser)
        }, failed: { strErrorMsg in
            completeHandler("failed", strErrorMsg, nil)
        })
    }
    
    //Show my recommended sites
    class func callShowMyRecommendedSites(_ dictParam: [AnyHashable : Any]?, handler completeHandler: @escaping blockDictWithMessage) {
        BaseJSONParser.baseService(withPostData: APP_SERVICE(strService: "ws_show_my_recommended_sites_for_engineer.php?"), withParam: dictParam, success: { dictParser in
            completeHandler(dictParser?["process_status"] as? String, dictParser?["process_message"] as? String, dictParser)
        }, failed: { strErrorMsg in
            completeHandler("failed", strErrorMsg, nil)
        })
    }
    
    
    //MARK: - TE
    
    //Home Screen
    class func callHomeScreenTE(_ dictParam: [AnyHashable : Any]?, handler completeHandler: @escaping blockDictWithMessage) {
        BaseJSONParser.baseService(withPostData: APP_SERVICE(strService: "ws_home_screen_details_for_te.php?"), withParam: dictParam, success: { dictParser in
            completeHandler(dictParser?["process_status"] as? String, dictParser?["process_message"] as? String, dictParser)
        }, failed: { strErrorMsg in
            completeHandler("failed", strErrorMsg, nil)
        })
    }
    
    //App Version
    class func callShowAppVersion(_ dictParam: [AnyHashable : Any]?, handler completeHandler: @escaping blockDictWithMessage) {
        BaseJSONParser.baseService(withPostData: APP_SERVICE(strService: "show_latest_app_version.php?"), withParam: dictParam, success: { dictParser in
            completeHandler(dictParser?["process_status"] as? String, dictParser?["process_message"] as? String, dictParser)
        }, failed: { strErrorMsg in
            completeHandler("failed", strErrorMsg, nil)
        })
    }
    
    //Pending Recomended site for TE
    class func callPendingRecommendedSiteForTE(_ dictParam: [AnyHashable : Any]?, handler completeHandler: @escaping blockDictWithMessage) {
        BaseJSONParser.baseService(withPostData: APP_SERVICE(strService: "ws_show_pending_recommended_site_for_te.php?"), withParam: dictParam, success: { dictParser in
            completeHandler(dictParser?["process_status"] as? String, dictParser?["process_message"] as? String, dictParser)
        }, failed: { strErrorMsg in
            completeHandler("failed", strErrorMsg, nil)
        })
    }
    
    //Approved Recomended site for TE
    class func callApprovedRecommendedSiteForTE(_ dictParam: [AnyHashable : Any]?, handler completeHandler: @escaping blockDictWithMessage) {
        BaseJSONParser.baseService(withPostData: APP_SERVICE(strService: "ws_show_approved_recommended_site_for_te.php?"), withParam: dictParam, success: { dictParser in
            completeHandler(dictParser?["process_status"] as? String, dictParser?["process_message"] as? String, dictParser)
        }, failed: { strErrorMsg in
            completeHandler("failed", strErrorMsg, nil)
        })
    }
    
    //Rejected Recomended site for TE
    class func callRejectedRecommendedSiteForTE(_ dictParam: [AnyHashable : Any]?, handler completeHandler: @escaping blockDictWithMessage) {
        BaseJSONParser.baseService(withPostData: APP_SERVICE(strService: "ws_show_rejected_recommended_site_for_te.php?"), withParam: dictParam, success: { dictParser in
            completeHandler(dictParser?["process_status"] as? String, dictParser?["process_message"] as? String, dictParser)
        }, failed: { strErrorMsg in
            completeHandler("failed", strErrorMsg, nil)
        })
    }
    
    //Reject Recomended Site by TE
    class func callRejectedRecommendedSiteByTE(_ dictParam: [AnyHashable : Any]?, handler completeHandler: @escaping blockDictWithMessage) {
        BaseJSONParser.baseService(withPostData: APP_SERVICE(strService: "ws_reject_recommended_site_for_te.php?"), withParam: dictParam, success: { dictParser in
            completeHandler(dictParser?["process_status"] as? String, dictParser?["process_message"] as? String, dictParser)
        }, failed: { strErrorMsg in
            completeHandler("failed", strErrorMsg, nil)
        })
    }
    
    //Show Mapped Engineers by TE
    class func callShowMappedEngineersByTE(_ dictParam: [AnyHashable : Any]?, handler completeHandler: @escaping blockDictWithMessage) {
        BaseJSONParser.baseService(withPostData: APP_SERVICE(strService: "ws_show_mapped_engineers_for_te.php?"), withParam: dictParam, success: { dictParser in
            completeHandler(dictParser?["process_status"] as? String, dictParser?["process_message"] as? String, dictParser)
        }, failed: { strErrorMsg in
            completeHandler("failed", strErrorMsg, nil)
        })
    }
    
    //Show Pending Engineers by TE
    class func callShowPendingEngineersByTE(_ dictParam: [AnyHashable : Any]?, handler completeHandler: @escaping blockDictWithMessage) {
        BaseJSONParser.baseService(withPostData: APP_SERVICE(strService: "ws_show_pending_engineers_for_te.php?"), withParam: dictParam, success: { dictParser in
            completeHandler(dictParser?["process_status"] as? String, dictParser?["process_message"] as? String, dictParser)
        }, failed: { strErrorMsg in
            completeHandler("failed", strErrorMsg, nil)
        })
    }
    
    
    //Gift Catalog for TE
    class func callGiftCatalogTE(_ dictParam: [AnyHashable : Any]?, handler completeHandler: @escaping blockDictWithMessage) {
        BaseJSONParser.baseService(withPostData: APP_SERVICE(strService: "ws_show_gift_catalog_for_te.php?"), withParam: dictParam, success: { dictParser in
            completeHandler(dictParser?["process_status"] as? String, dictParser?["process_message"] as? String, dictParser)
        }, failed: { strErrorMsg in
            completeHandler("failed", strErrorMsg, nil)
        })
    }
    
    //Show notification for TE
    class func callNotificationForTE(_ dictParam: [AnyHashable : Any]?, handler completeHandler: @escaping blockDictWithMessage) {
        BaseJSONParser.baseService(withPostData: APP_SERVICE(strService: "show_notifications_for_te.php?"), withParam: dictParam, success: { dictParser in
            completeHandler(dictParser?["process_status"] as? String, dictParser?["process_message"] as? String, dictParser)
        }, failed: { strErrorMsg in
            completeHandler("failed", strErrorMsg, nil)
        })
    }
    
    //Approve/Reject Engineers by TE
    class func callApproveRejectEngineerByTE(_ dictParam: [AnyHashable : Any]?, handler completeHandler: @escaping blockDictWithMessage) {
        BaseJSONParser.baseService(withPostData: APP_SERVICE(strService: "ws_approve_reject_the_engineer_for_te_V2.php?"), withParam: dictParam, success: { dictParser in
            completeHandler(dictParser?["process_status"] as? String, dictParser?["process_message"] as? String, dictParser)
        }, failed: { strErrorMsg in
            completeHandler("failed", strErrorMsg, nil)
        })
    }
    
    //Show Approved Mapped Engineers
    class func callShowApprovedMappedEngineers(_ dictParam: [AnyHashable : Any]?, handler completeHandler: @escaping blockDictWithMessage) {
        BaseJSONParser.baseService(withPostData: APP_SERVICE(strService: "ws_show_approved_mapped_engineers_for_te.php?"), withParam: dictParam, success: { dictParser in
            completeHandler(dictParser?["process_status"] as? String, dictParser?["process_message"] as? String, dictParser)
        }, failed: { strErrorMsg in
            completeHandler("failed", strErrorMsg, nil)
        })
    }
    
    //Add site recommendation for TE
    class func callAddSiteRecommendationForTE(_ dictParam: [AnyHashable : Any]?, handler completeHandler: @escaping blockDictWithMessage) {
        BaseJSONParser.baseService(withPostData: APP_SERVICE(strService: "ws_add_site_recommendation_for_te.php?"), withParam: dictParam, success: { dictParser in
            completeHandler(dictParser?["process_status"] as? String, dictParser?["process_message"] as? String, dictParser)
        }, failed: { strErrorMsg in
            completeHandler("failed", strErrorMsg, nil)
        })
    }
    
    //Send Mail To ASM by TE
    class func callSendMailToASMByTE(_ dictParam: [AnyHashable : Any]?, handler completeHandler: @escaping blockDictWithMessage) {
        BaseJSONParser.baseService(withPostData: APP_SERVICE(strService: "ws_send_mail_to_asm_for_confirm_site_for_te.php?"), withParam: dictParam, success: { dictParser in
            completeHandler(dictParser?["process_status"] as? String, dictParser?["process_message"] as? String, dictParser)
        }, failed: { strErrorMsg in
            completeHandler("failed", strErrorMsg, nil)
        })
    }
    
    //Branch zone list for TE
    class func callBranchZoneListForTE(_ dictParam: [AnyHashable : Any]?, handler completeHandler: @escaping blockDictWithMessage) {
        BaseJSONParser.baseService(withPostData: APP_SERVICE(strService: "branch_zone_list_for_te_V2.php?"), withParam: dictParam, success: { dictParser in
            completeHandler(dictParser?["process_status"] as? String, dictParser?["process_message"] as? String, dictParser)
        }, failed: { strErrorMsg in
            completeHandler("failed", strErrorMsg, nil)
        })
    }
    
}
