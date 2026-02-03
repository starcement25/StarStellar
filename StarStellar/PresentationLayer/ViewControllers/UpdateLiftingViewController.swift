//
//  UpdateLiftingViewController.swift
//  StarStellar
//
//  Created by Sanjeet Kumar on 20/09/22.
//  Copyright © 2022 Apple. All rights reserved.
//

import UIKit
import SVProgressHUD
import SwiftyJSON
import SDWebImage

class UpdateLiftingViewController: BaseViewController, UIGestureRecognizerDelegate {
    
    @IBOutlet weak var tblViewEnginners: UITableView!
    @IBOutlet var viewExistingSiteList: UIView!
    @IBOutlet weak var tblViewSites: UITableView!
    var arrMyRecommendedSite : [JSON] = []
    var arrMyRecommendedSiteConst : [JSON] = []
    var arrMappedEngineer : [JSON] = []
    var intPageNo = 1
    var strSelectedEnggId = ""    
    
    //MARK: - View Life Cycle
    
    override func viewDidLoad() {
        super.viewDidLoad()
        designView()
        loadData()
    }
    
    //MARK: - Initialization Method
    
    func designView() -> Void {
        tblViewEnginners.register(UINib(nibName: "UpdateLiftingCell", bundle: nil), forCellReuseIdentifier: "cellUpdateLifting")
        tblViewEnginners.separatorColor = .clear
        tblViewSites.register(UINib(nibName: "UpdateLiftingCell", bundle: nil), forCellReuseIdentifier: "cellUpdateLifting")
        tblViewSites.separatorColor = .clear
    }
    
    func loadData() -> Void {
        wsShowApprovedMappedEngineers()
    }
    
    //MARK: - IBAction's
    
    @IBAction func btnBackClicked(_ sender: Any) {
        navigationController?.popViewController(animated: true)
    }
    
    //MARK: - Web Service
    
    func wsShowApprovedMappedEngineers() -> Void {
        
        if isServerReachable(){
            //te_code,search_term,page_no
            var dict: [String : Any] = [:]
            dict["te_code"] = Defaults.teCode()
            dict["search_term"] = ""
            dict["page_no"] = intPageNo
            
            SVProgressHUD.show()
            SSParserLayer.callShowApprovedMappedEngineers(dict, handler: { [self] strStatus, strMessage, dictResponse in
                SVProgressHUD.dismiss()
                if (strStatus == "YES") {
                    let json = JSON(dictResponse!)
                    arrMappedEngineer += json["engineer_data"].arrayValue
                    intPageNo += 1
                    tblViewEnginners.reloadData()
                }else{
                    self.showToastAlert(strMessage ?? StringConstant.kErrorMsg)
                }
            })
            
        }else{
            showToastAlert(StringConstant.kNoInternet)
        }
    }
    
    func wsShowRecommendedSitesForEngineer(_ strEngineerId : String) -> Void {
        
        if isServerReachable(){
            
            var dict: [String : String] = [:]
            dict["the_engineer_id"] = strEngineerId
            
            SVProgressHUD.show()
            SSParserLayer.callShowMyRecommendedSites(dict, handler: { [self] strStatus, strMessage, dictResponse in
                SVProgressHUD.dismiss()
                if (strStatus == "YES") {
                    let json = JSON(dictResponse!)
                    print(json)
                    arrMyRecommendedSite = json["my_recommended_site_data"].arrayValue
                    arrMyRecommendedSiteConst = json["my_recommended_site_data"].arrayValue
                    showRecommandedSites()
                }else{
                    self.showToastAlert(strMessage ?? StringConstant.kErrorMsg)
                }
            })
            
        }else{
            showToastAlert(StringConstant.kNoInternet)
        }
    }
    
    //MARK: - Helper Method
    
    func showRecommandedSites() -> Void {
        
//        let alert = UIAlertController(title: "Existing Site List", message: nil, preferredStyle: .actionSheet)
//        for (index, dictSites) in arrSites.enumerated() {
//            
//          print("Item \(index): \(dictSites)")
//            let action = UIAlertAction(title: dictSites["r_site_name"].stringValue, style: .default) {[self] alertAction in
//                print(alertAction.accessibilityValue ?? "")
//                let index = "\(alertAction.accessibilityValue ?? "")"
//                performSegue(withIdentifier: "updateLiftingToRecommSiteDetails", sender: index)
//            }
//            action.accessibilityValue = "\(index)"
//            alert.addAction(action)
//        }
//        alert.addAction(UIAlertAction(title: "Cancel", style: .cancel))
//        present(alert, animated: true)
        
        let viewBase = UIView.init(frame: CGRect(x: 0, y: 0, width: view.frame.size.width, height: view.frame.size.height))
        viewExistingSiteList.frame = CGRect(x: 20, y: 0, width: view.frame.size.width - 40, height: view.frame.size.height - 58);
        viewExistingSiteList.layer.cornerRadius = 5.0;
        viewExistingSiteList.layer.masksToBounds = true;
        viewBase.backgroundColor = UIColor.black.withAlphaComponent(0.3)
        viewBase.addSubview(viewExistingSiteList)
        viewExistingSiteList.center = viewBase.center
        
        let tapGesture = UITapGestureRecognizer(target: self, action: #selector(self.handleTap(_:)))
        tapGesture.delegate = self
        viewBase.addGestureRecognizer(tapGesture)
        
        view.addSubview(viewBase)
        tblViewSites.reloadData()
    }
    
    //MARK: - Segue
    
    override func prepare(for segue: UIStoryboardSegue, sender: Any?) {
        if segue.identifier == "updateLiftingToRecommSiteDetails" {
            let teRDUL = segue.destination as? TERecommSiteDetailsUpdateLifting
            let index = sender as! Int
            teRDUL?.dictSiteDetails = arrMyRecommendedSite[index]
            teRDUL?.strEngineerId = strSelectedEnggId
        }
    }
    
    //MARK: - Gesture
    
    @objc func handleTap(_ sender: UITapGestureRecognizer? = nil) {
        sender?.view?.removeFromSuperview()
    }
    
    func gestureRecognizer(_ gestureRecognizer: UIGestureRecognizer, shouldReceive touch: UITouch) -> Bool {
        return touch.view == gestureRecognizer.view
    }
}

//MARK: - UITableView Delegate and DataSource

extension UpdateLiftingViewController : UITableViewDelegate, UITableViewDataSource {
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        if tableView == tblViewEnginners {
            return arrMappedEngineer.count
        }else{
            //Sites
            return arrMyRecommendedSite.count
        }
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cellIdentifier = "cellUpdateLifting"
        let cell = tableView.dequeueReusableCell(withIdentifier: cellIdentifier, for: indexPath) as? UpdateLiftingCell
        
        if tableView == tblViewEnginners {
            //let cell = tblViewEnginners.dequeueReusableCell(withIdentifier: cellIdentifier, for: indexPath) as? UpdateLiftingCell
            let dictEngineer = arrMappedEngineer[indexPath.row].dictionaryValue
            cell?.imgView.sd_setImage(with: URL(string: dictEngineer["e_profile_image_url"]?.stringValue ?? ""), placeholderImage: UIImage(named: "user_placeholder"))
            cell?.lblCompanyName.text = dictEngineer["e_name"]?.stringValue
            cell?.lblMobileNumber.text = dictEngineer["e_mobile"]?.stringValue
            cell?.lblPlace.text = dictEngineer["e_city_town"]?.stringValue
            return cell ?? UITableViewCell()
        }else{
            //let cell = tblViewEnginners.dequeueReusableCell(withIdentifier: cellIdentifier, for: indexPath) as? UpdateLiftingCell
            let dictSites = arrMyRecommendedSite[indexPath.row].dictionaryValue
            cell?.imgView.sd_setImage(with: URL(string: dictSites["r_recomended_site_image_url"]?.stringValue ?? ""), placeholderImage: UIImage(named: "image_placeholder"))
            cell?.lblCompanyName.text = dictSites["r_site_name"]?.stringValue
            cell?.lblMobileNumber.text = dictSites["r_mobile_no"]?.stringValue
            cell?.lblPlace.text = dictSites["r_address"]?.stringValue
            return cell ?? UITableViewCell()
        }
    }
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        if tableView == tblViewEnginners {
            let dictEngineer = arrMappedEngineer[indexPath.row].dictionaryValue
            strSelectedEnggId = dictEngineer["eid"]?.stringValue ?? ""
            wsShowRecommendedSitesForEngineer(dictEngineer["eid"]?.stringValue ?? "")
        }else{
            //Site
            performSegue(withIdentifier: "updateLiftingToRecommSiteDetails", sender: indexPath.row)
        }
        
    }
}

//MARK: - UISearchBar Delegate

extension UpdateLiftingViewController : UISearchBarDelegate {
    func searchBar(_ searchBar: UISearchBar, textDidChange searchText: String) {
        arrMyRecommendedSite = arrMyRecommendedSiteConst.filter( { $0["r_site_name"].stringValue.range(of: searchText, options: .caseInsensitive) != nil || $0["r_mobile_no"].stringValue.range(of: searchText, options: .caseInsensitive) != nil})
        if searchText.count == 0 {
            arrMyRecommendedSite = arrMyRecommendedSiteConst
        }
        tblViewSites.reloadData()
    }
    
    func searchBarSearchButtonClicked(_ searchBar: UISearchBar) {
        self.view.endEditing(true)
    }
}
