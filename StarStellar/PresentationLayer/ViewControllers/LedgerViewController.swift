//
//  LedgerViewController.swift
//  StarStellar
//
//  Created by Apple on 08/08/19.
//  Copyright © 2019 Apple. All rights reserved.
//

import UIKit
import SwiftyJSON
import SVProgressHUD

class LedgerViewController: BaseViewController {
    
    @IBOutlet weak var rightBarButtonNetPoints: UIBarButtonItem!
    @IBOutlet weak var tblViewLedger: UITableView!
    var arrLedger = [JSON]()
    var intPageLedger = 1
    var strEngineerId = ""
    
    //MARK: - View Life Cycle
    
    override func viewDidLoad() {
        super.viewDidLoad()
        self.designView()
        self.loadData()
    }
    
    //MARK: - Initialization Method
    
    func designView() -> Void {
        
        rightBarButtonNetPoints.setTitleTextAttributes([ NSAttributedString.Key.font: UIFont.systemFont(ofSize: 12, weight: UIFont.Weight.semibold)], for: UIControl.State.normal)
        tblViewLedger.register(UINib(nibName: "LedgerCell", bundle: nil), forCellReuseIdentifier: "cell")
        tblViewLedger.tableFooterView = UIView()
    }
    
    func loadData() -> Void {
        callShowMyLedger()
    }
    
    //MARK: - WebService
    
    func callShowMyLedger() -> Void {
        
        if isServerReachable() {
            //the_engineer_id,page_no
            var dict : [String : Any] = [:]
            dict["the_engineer_id"] = strEngineerId
            dict["page_no"] = intPageLedger
            
            SVProgressHUD.show()
            SSParserLayer.callShowMyLedger(dict) { (strStatus, strMessage, dictResponse) in
                SVProgressHUD.dismiss()
                if strStatus == "YES" {
                    self.intPageLedger += 1
                    let json = JSON(dictResponse!)
                    self.rightBarButtonNetPoints.title = "Net Points : \(json["total_epoint"].stringValue)"
                    if let navController = self.navigationController, navController.viewControllers.count >= 2 {
                        let viewController = navController.viewControllers[navController.viewControllers.count - 2]
                        if (viewController is EngineerProfileViewController){
                            self.rightBarButtonNetPoints.title = ""
                        }
                    }
                    self.arrLedger += json["ledger_data"].arrayValue
                    self.tblViewLedger.reloadData()
                }else{
                    self.showToastAlert(strMessage ?? StringConstant.kErrorMsg)
                }
            }
            
        }else{
            showToastAlert(StringConstant.kNoInternet)
        }
        
    }
    
    //MARK: - IBAction's
    
    @IBAction func btnBackClicked(_ sender: UIBarButtonItem) {
        navigationController?.popViewController(animated: true)
    }
    
    //MARK: - Segue
    
    override func prepare(for segue: UIStoryboardSegue, sender: Any?) {
        if segue.identifier == "stellarPointsToSiteRecommendationDetails" {
            if let indexPath = tblViewLedger.indexPathForSelectedRow{
                let selectedRow = indexPath.row
                let msdvc = segue.destination as! MySiteDetailsViewController
                msdvc.dictSite = arrLedger[selectedRow]["related_data"]
            }
        }
    }
    
}


extension LedgerViewController : UITableViewDelegate, UITableViewDataSource{
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return arrLedger.count
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cellIdentifier = "cell"
        let cell = tableView.dequeueReusableCell(withIdentifier: cellIdentifier, for: indexPath) as? LedgerCell
        
        cell?.lblDate.text = arrLedger[indexPath.row]["ldgr_datetime"].stringValue
        cell?.lblDescription.text = arrLedger[indexPath.row]["description"].stringValue
        //cell?.lblDescription.text = "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat."
        cell?.lblEarn.text = arrLedger[indexPath.row]["point_earned"].stringValue
        cell?.lblReedem.text = arrLedger[indexPath.row]["point_redeem"].stringValue
        
        return cell!
        
    }
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        if arrLedger[indexPath.row]["ldgr_type"].stringValue == "SITE_RECOMENDATION" {
            performSegue(withIdentifier: "stellarPointsToSiteRecommendationDetails", sender: self)
        }
    }
    
    func scrollViewDidEndDragging(_ scrollView: UIScrollView, willDecelerate decelerate: Bool) {
        
        // UITableView only moves in one direction, y axis
        let currentOffset = scrollView.contentOffset.y
        let maximumOffset = scrollView.contentSize.height - scrollView.frame.size.height
        
        // Change 10.0 to adjust the distance from bottom
        if maximumOffset - currentOffset <= 10.0 {
            callShowMyLedger()
        }
    }
    
    func tableView(_ tableView: UITableView, estimatedHeightForRowAt indexPath: IndexPath) -> CGFloat {
        return UITableView.automaticDimension
    }
    
}

