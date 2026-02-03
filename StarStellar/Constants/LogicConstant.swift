//
//  LogicConstant.swift
//  BaseSwift
//
//  Created by Apple on 16/05/19.
//  Copyright © 2019 Apple. All rights reserved.
//

import Foundation

//MARK:--------Enable or Disable Print in swift-----------//
func print(_ items: Any..., separator: String = " ", terminator: String = "\n") {
    //To disable print comment below line
    //Swift.print(items[0], separator:separator, terminator: terminator)
}

class LogicConstant: NSObject {
    
    func validateMobileNumber(_ strMobile: String?) -> Bool {
        let regexString = "^[6789]\\d{9}$"
        let pred = NSPredicate(format: "self matches[cd] %@", regexString)
        return pred.evaluate(with: strMobile)
    }
    
    func validateEmail(emailStr:String) -> Bool {
        let emailRegEx = "[A-Z0-9a-z._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,64}"
        
        let emailPred = NSPredicate(format:"SELF MATCHES %@", emailRegEx)
        return emailPred.evaluate(with: emailStr)
    }
    
    func convertDateFormat(_ strDate : String) -> String {
        
        //2019-07-24 12:21:12 to 24th July,2019
        
        let dateFormater = DateFormatter()
        dateFormater.dateFormat = "yyyy-MM-dd HH:mm:ss"
        let date: Date? = dateFormater.date(from: strDate)
        // Convert date object into desired format
        dateFormater.dateFormat = "dd-MMM-yyyy"
        var newDateString: String = ""
        if let date = date {
            
            newDateString = dateFormater.string(from: date)            
            let arrDateComponent = newDateString.components(separatedBy: "-")
            let strDaySuffix = daySuffix(from: date)
            newDateString = String(format: "%@%@ %@,%@", arrDateComponent[0],strDaySuffix,arrDateComponent[1],arrDateComponent[2])
            
        }        
        return newDateString
    }
    
    func daySuffix(from date: Date) -> String {
        let calendar = Calendar.current
        let dayOfMonth = calendar.component(.day, from: date)
        switch dayOfMonth {
        case 1, 21, 31: return "st"
        case 2, 22: return "nd"
        case 3, 23: return "rd"
        default: return "th"
        }
    }
    
    
}
