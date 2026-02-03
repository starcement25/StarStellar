//
//  BaseJSONParser.swift
//  BaseAppSwift
//
//  Created by Apple on 17/07/19.
//  Copyright © 2019 Apple. All rights reserved.
//

import UIKit
import Alamofire

typealias ParserSuccess = ([AnyHashable : Any]?) -> Void
typealias ParserError = (String?) -> Void



class BaseJSONParser: NSObject { 
    
    class func baseService(withPostData strUrl: String?, withParam dictParam: [AnyHashable : Any]?, success blockSuccess: @escaping ParserSuccess, failed blockError: @escaping ParserError) {
        AF.request(
            strUrl!,
            method: .post,
            parameters: dictParam as? Parameters,
            encoding: URLEncoding.default,
            headers: nil
        ).responseJSON { response in
            switch response.result {
            case .success(let value):
                blockSuccess(value as? [AnyHashable: Any])
            case .failure(let error):
                blockError(error.localizedDescription)
                print(error)
            }
        }
    }
}
