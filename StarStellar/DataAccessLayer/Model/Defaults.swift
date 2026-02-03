//
//  Defaults.swift
//  StarStellar
//
//  Created by Apple on 26/07/19.
//  Copyright © 2019 Apple. All rights reserved.
//

import Foundation
import SwiftyJSON




struct Defaults {
    
    static func setUserData(_ json : JSON) -> Void {        
        UserDefaults.standard.set(json["the_engineer_id"].stringValue,  forKey: "the_engineer_id")
        UserDefaults.standard.set(json["e_name"].stringValue,           forKey: "e_name")
        UserDefaults.standard.set(json["e_mobile"].stringValue,         forKey: "mobile_number")
        UserDefaults.standard.set(json["te_code"].stringValue,          forKey: "te_code")
        UserDefaults.standard.set(json["e_email"].stringValue,          forKey: "e_email")
        UserDefaults.standard.set(json["e_dob"].stringValue,            forKey: "e_dob")
        UserDefaults.standard.set(json["e_dom"].stringValue,            forKey: "e_dom")
        UserDefaults.standard.set(json["e_address"].stringValue,        forKey: "e_address")
        UserDefaults.standard.set(json["e_pin"].stringValue,            forKey: "e_pin")
        UserDefaults.standard.set(json["e_state"].stringValue,          forKey: "e_state")
        UserDefaults.standard.set(json["e_city_town"].stringValue,      forKey: "e_city_town")
        UserDefaults.standard.set(json["e_profile_image"].stringValue,  forKey: "e_profile_image")
        UserDefaults.standard.synchronize()
    }
    
    static func userType() -> String {
        return UserDefaults.standard.string(forKey: "user_type") ?? ""   
    }
    
    static func mobileNumber() -> String {
        return UserDefaults.standard.string(forKey: "mobile_number") ?? ""
    }
    
    static func deviceToken() -> String {
        return UserDefaults.standard.string(forKey: "device_token") ?? ""
    }
    
    static func engineerId() -> String {
        return UserDefaults.standard.string(forKey: "the_engineer_id") ?? ""
    }
    
    static func engineerName() -> String {
        return UserDefaults.standard.string(forKey: "e_name") ?? ""
    }
    
    static func engineerEmail() -> String {
        return UserDefaults.standard.string(forKey: "e_email") ?? ""
    }
    
    static func engineerDOB() -> String {
        return UserDefaults.standard.string(forKey: "e_dob") ?? ""
    }
    
    static func engineerDOM() -> String {
        return UserDefaults.standard.string(forKey: "e_dom") ?? ""
    }
    
    static func engineerAddress() -> String {
        return UserDefaults.standard.string(forKey: "e_address") ?? ""
    }
    
    static func engineerPincode() -> String {
        return UserDefaults.standard.string(forKey: "e_pin") ?? ""
    }
    
    static func engineerState() -> String {
        return UserDefaults.standard.string(forKey: "e_state") ?? ""
    }
    
    static func engineerCity() -> String {
        return UserDefaults.standard.string(forKey: "e_city_town") ?? ""
    }
    
    static func engineerProfileImage() -> String {
        return UserDefaults.standard.string(forKey: "e_profile_image") ?? ""
    }
    
    static func teCode() -> String {
        return UserDefaults.standard.string(forKey: "te_code") ?? ""
    }
    
    static func loggedInType() -> String {
        return UserDefaults.standard.string(forKey: "logged_in_type") ?? ""
    }
    
    static func flagLoggedIn() -> Bool {
        return UserDefaults.standard.bool(forKey: "logged_in")
    }
    
    //TE Response
    /*{
     "the_te_id" : "1",
     "process_message" : "Success.",
     "the_te_email" : "mriduj@coral.in",
     "process_status" : "YES",
     "user_type" : "TE",
     "the_te_mobile_no" : "9831722939",
     "the_te_code" : "TE001",
     "the_te_name" : "Mridu",
     "te_profile_image" : "http:\/\/starsaathi.com\/starstellar\/te_profile_pic\/profile.png"
     }*/
    
    static func TEId() -> String {
        return UserDefaults.standard.string(forKey: "the_te_id") ?? ""
    }
    
    static func TEEmail() -> String {
        return UserDefaults.standard.string(forKey: "the_te_email") ?? ""
    }
    
    static func TEMobile() -> String {
        return UserDefaults.standard.string(forKey: "the_te_mobile_no") ?? ""
    } 
    
    static func TEName() -> String {
        return UserDefaults.standard.string(forKey: "the_te_name") ?? ""
    }
    
    static func TEProfileImage() -> String {
        return UserDefaults.standard.string(forKey: "te_profile_image") ?? ""
    }
    
    
}
