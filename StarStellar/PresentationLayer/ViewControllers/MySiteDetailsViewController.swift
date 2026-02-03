//
//  MySiteDetailsViewController.swift
//  StarStellar
//
//  Created by Apple on 30/07/19.
//  Copyright © 2019 Apple. All rights reserved.
//

import UIKit
import Alamofire
import SVProgressHUD
import SwiftyJSON
import SDWebImage

//["Site name:","Site Address:","Site Potential:","Contact Person:","Contact Person Category:","Mobile:"]
enum SelectedValue : Int {
    case SiteName = 0
    case SiteAddress = 1
    case SitePotential = 2
    case ContactPerson = 3
    case ContactPersonCategory = 4
    case Mobile = 5
}


class MySiteDetailsViewController: BaseTableViewController {
    
    
    var arrLabel = [String]()
    var arrValue = [String]()
    
    var dictSite : JSON = []
    @IBOutlet weak var imgViewSite: UIImageView!
    
    
    
    //MARK: - View Life Cycle
    override func viewDidLoad() {
        super.viewDidLoad()
        designView()
        loadData()
    }
    
    override func viewWillAppear(_ animated: Bool) {
        navigationController?.setNavigationBarHidden(false, animated: true)
    }
    
    //MARK: - Initialization Method
    
    func designView() -> Void {
        tableView.rowHeight = UITableView.automaticDimension
        tableView.estimatedRowHeight = 30.0
        
        tableView.register(UINib(nibName: "MySiteDetailsCell", bundle: nil), forCellReuseIdentifier: "cell")
        tableView.separatorColor = UIColor.clear
        
    }
    
    func loadData() -> Void {
        
        /*{
         "r_submission_date" : "2019-07-29 17:46:16",
         "r_site_name" : "test",
         "r_status" : "PENDING",
         "r_site_id" : "13",
         "r_contact_person_category_name" : "Contractor",
         "r_te_code" : "TE001",
         "r_contact_person_name" : "vola",
         "r_recomended_site_image_url" : "",
         "r_address" : "kol",
         "r_site_potential_in_mt" : "8",
         "r_mobile_no" : "9233333333",
         "r_te_name" : "Mridu"
         }*/
        
        print(dictSite)
        
        arrLabel = ["Site name:",
                    "Site Address:",
                    "Site Potential:",
                    "Contact Person:",
                    "Contact Person Category:",
                    "Mobile:",
                    "Verified by TE:",
                    "Actual Product Name:",
                    "Purchased From:",
                    "Purchased From Name:",
                    "Purchased From Area:",
                    "Purchased From Contact No:"]
        
        arrValue = [dictSite["r_site_name"].stringValue,
                    dictSite["r_address"].stringValue,
                    dictSite["r_site_potential_in_mt"].stringValue,
                    dictSite["r_contact_person_name"].stringValue,
                    dictSite["r_contact_person_category_name"].stringValue,
                    dictSite["r_mobile_no"].stringValue,
                    dictSite["actual_consumption"].stringValue,
                    dictSite["actual_product_name"].stringValue,
                    dictSite["purchased_from"].stringValue,
                    dictSite["purchased_from_name"].stringValue,
                    dictSite["purchased_from_area"].stringValue,
                    dictSite["purchased_from_contact_no"].stringValue]
        
        imgViewSite.sd_setImage(with: URL(string: dictSite["r_recomended_site_image_url"].stringValue), placeholderImage: UIImage(named: "image_placeholder"))
        
//        request(dictSite["r_recomended_site_image_url"].stringValue, method: .get)
//            .validate()
//            .responseData(completionHandler: { (responseData) in
//                self.imgViewSite.image = UIImage(data: responseData.data!)
//            })
    }
    
    
    //MARK: - IBAction's
    
    @IBAction func btnBackClicked(_ sender: UIBarButtonItem) {
        navigationController?.popViewController(animated: true)
    }
    
    //MARK: - Table view data source
    
    override func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        // #warning Incomplete implementation, return the number of rows
        return arrLabel.count
    }
    
    override func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCell(withIdentifier: "cell", for: indexPath) as? MySiteDetailsCell
        cell?.lblStatic.text = arrLabel[indexPath.row]
        if arrLabel[indexPath.row] == "Verified by TE:" {
            cell?.lblValue.text = "\(arrValue[indexPath.row]) bags"
        }else{
            cell?.lblValue.text = arrValue[indexPath.row]
        }
        return cell!
    }
    
    override func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        
        
        switch indexPath.row {
        case SelectedValue.SiteAddress.rawValue:
            print("site address")
            
            var strAddress = arrValue[indexPath.row]
            strAddress = strAddress.replacingOccurrences(of: "\n", with: "").replacingOccurrences(of: ",", with: " ")
            if let addresswithPercentEscapes = strAddress.addingPercentEncoding(withAllowedCharacters: .urlPathAllowed) {
                
                let urlwithPercentEscapes = "http://maps.google.com/maps?q=\(addresswithPercentEscapes)"
                let url = URL(string: urlwithPercentEscapes)
                if let url = url {
                    if UIApplication.shared.canOpenURL(url) {
                        UIApplication.shared.open(url, options: [:], completionHandler:nil)
                    }
                }
            }
            
        case SelectedValue.Mobile.rawValue:
            
            let strMobile = arrValue[indexPath.row]
            if let url = URL(string: "tel://\(strMobile)"),
                UIApplication.shared.canOpenURL(url) {
                if #available(iOS 10, *) {
                    UIApplication.shared.open(url, options: [:], completionHandler:nil)
                } else {
                    UIApplication.shared.openURL(url)
                }
            } else {
                showToastAlert(StringConstant.kErrorMsg)
            }
            
        default:
            print("Default")
        }
        
    }
    
}
