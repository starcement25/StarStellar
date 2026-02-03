//
//  EngineerProfileViewController.swift
//  StarStellar
//
//  Created by Apple on 22/08/19.
//  Copyright © 2019 Apple. All rights reserved.
//

import UIKit
import SVProgressHUD
import SwiftyJSON
import Alamofire
import SDWebImage

class EngineerProfileViewController: BaseViewController, UITableViewDataSource, UITableViewDelegate {

    @IBOutlet weak var tblViewProfile: UITableView!
    @IBOutlet weak var imgViewUser: UIImageView!
    @IBOutlet weak var lblSites: UILabel!
    @IBOutlet weak var lblPoints: UILabel!
    @IBOutlet weak var lblGifts: UILabel!
    @IBOutlet weak var lblUsername: UILabel!
    @IBOutlet weak var viewUpper: UIView!
    
    var arrProfileData : [JSON] = []
    var dictProfile : JSON = []
    
    
    
    //MARK: - View Life Cycle
    
    override func viewDidLoad() {
        super.viewDidLoad()
        self.designView()
        self.loadData()
    }  
    
    //MARK: - Initialization Method
    
    func designView() -> Void {
        tblViewProfile.register(UINib(nibName: "ProfileCell", bundle: nil), forCellReuseIdentifier: "cell")
    }
    
    func loadData() -> Void {
        callShowProfileForEngineer()
    }
    
    //MARK: - UITableView Delegate and DataSource
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return arrProfileData.count
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cellIdentifier = "cell"
        let cell = tableView.dequeueReusableCell(withIdentifier: cellIdentifier, for: indexPath) as? ProfileCell
        cell?.lblItem.text = String(format: "%@:", arrProfileData[indexPath.row]["label"].stringValue)
        cell?.lblDetails.text = arrProfileData[indexPath.row]["value"].stringValue
        return cell!
    }
    
    //MARK: - IBAction's
    
    @IBAction func btnBackClicked(_ sender: UIBarButtonItem) {
        navigationController?.popViewController(animated: true)
    }
    
    //MARK: - Gesture
    
    @IBAction func viewSiteRecommendedTapped(_ sender: UITapGestureRecognizer) {
        performSegue(withIdentifier: "engineerProfileToSiteRecommended", sender: self)
    }
    
    @IBAction func viewStellarPointsTapped(_ sender: UITapGestureRecognizer) {
        performSegue(withIdentifier: "engineerProfileToLedger", sender: self)
    }
    
    @IBAction func viewGiftsRedeemedTapped(_ sender: UITapGestureRecognizer) {
        performSegue(withIdentifier: "engineerProfileToMyOrders", sender: self)
    }
    
    //MARK: - Web Service
    
    func callShowProfileForEngineer() -> Void {
        
        if isServerReachable(){
            
            var dict: [String : Any] = [:]
            dict["the_engineer_id"] = dictProfile["eid"].stringValue
            
            SVProgressHUD.show()
            SSParserLayer.callShowProfileForEngineer(dict, handler: { strStatus, strMessage, dictResponse in
                SVProgressHUD.dismiss()
                if (strStatus == "YES") {
                    let json = JSON(dictResponse!)
                    //self.dictProfile = json
                    self.setData(dict: json)
                    print("-->>",json)
                }else{
                    self.showToastAlert(strMessage ?? StringConstant.kErrorMsg)
                }
            })
            
        }else{
            showToastAlert(StringConstant.kNoInternet)
        }
    }
    
    //MARK: - Set Data
    
    func setData(dict : JSON) -> Void {
        
        Defaults.setUserData(dict)
        
        lblUsername.text = dict["e_name"].stringValue
        lblSites.text    = dict["number_of_sites"].stringValue
        lblPoints.text   = dict["number_of_points"].stringValue
        lblGifts.text    = dict["number_of_gifts"].stringValue
        imgViewUser.sd_setImage(with: URL(string: dict["e_profile_image"].stringValue), placeholderImage: UIImage(named: "user_placeholder"))
        
        
//        request(dict["e_profile_image"].stringValue, method: .get)
//            .validate()
//            .responseData(completionHandler: { (responseData) in
//                self.imgViewUser.image = UIImage(data: responseData.data!)
//            })
        
        arrProfileData = dict["profile_data"].arrayValue
        tblViewProfile.reloadData()
        
    }
    
    //MARK: - Segue
    
    override func prepare(for segue: UIStoryboardSegue, sender: Any?) {
        if (segue.identifier == "engineerProfileToSiteRecommended") {
            let esrvc = segue.destination as? EngineerSiteRecommendedVC
            print(dictProfile)
            print(dictProfile["eid"].stringValue)
            esrvc?.strEngineerId = dictProfile["eid"].stringValue
        }else if segue.identifier == "engineerProfileToLedger" {
            
            let lvc = segue.destination as? LedgerViewController
            lvc?.strEngineerId = dictProfile["eid"].stringValue
            
            
        }else if segue.identifier == "engineerProfileToMyOrders" {
            let myvc = segue.destination as? MyOrdersViewController
            myvc?.strEngineerId = dictProfile["eid"].stringValue
            myvc?.title = "Gift Redeemed"
        }
    } 
    

    

}
