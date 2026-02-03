//
//  StringConstant.swift
//  BaseSwift
//
//  Created by Apple on 16/05/19.
//  Copyright © 2019 Apple. All rights reserved.
//

import Foundation

struct StringConstant {
    
    struct Url {
        //static let baseURL = "http://starsaathi.com/starstellar/"
        static let baseURL = "https://www.starstellar.com/"
        // static let baseURL = "https://dev.starstellar.com/"
        static let aboutURL = "https://www.starstellar.com/about_weblink.php"
        static let TermsAndConditionsURL = "https://www.starstellar.com/tnc_weblink.php"
        
    }
    
    struct Path {
        static let Tmp = NSTemporaryDirectory()
        static let Home = NSHomeDirectory()
    }
    
    struct Device {
        static let Id = UUID().uuidString
        static let DeviceType = "IOS"
    }
    
    struct App {
        static let Version = Bundle.main.infoDictionary?["CFBundleShortVersionString"] as? String
    }
    
    static let kNoInternet = "Cannot connect to the server - please check your Internet connection and try again."
    static let kErrorMsg = "Something went wrong"
    static let kAppName  = "Star Stellar"
    
}
