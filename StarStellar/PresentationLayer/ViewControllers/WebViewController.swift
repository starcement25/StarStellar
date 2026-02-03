//
//  WebViewController.swift
//  StarStellar
//
//  Created by Apple on 23/08/19.
//  Copyright © 2019 Apple. All rights reserved.
//

import UIKit
import WebKit

class WebViewController: BaseViewController, WKNavigationDelegate {

    @IBOutlet weak var activityIndicator: UIActivityIndicatorView!
    @IBOutlet weak var webView: WKWebView!
    var strWeblink = ""
    
    //MARK: - View Life Cycle
    
    override func viewDidLoad() {
        super.viewDidLoad()
        self.designView()
        self.loadData()
    }
    
    override func viewWillAppear(_ animated: Bool) {
        navigationController?.setNavigationBarHidden(false, animated: true)        
    }
    
    //MARK: - Initialization Method
    
    func designView() -> Void {
        activityIndicator.hidesWhenStopped = true
        webView.navigationDelegate = self
    }
    
    func loadData() -> Void {
        activityIndicator.startAnimating()
        let url = URL(string: strWeblink)!
        webView.load(URLRequest(url: url))
        webView.allowsBackForwardNavigationGestures = true
    }
    
    //MARK: - IBAction's
    @IBAction func btnBackClicked(_ sender: UIBarButtonItem) {
        navigationController?.popViewController(animated: true)
    }
    
    //MARK: - WKWebView Delegate
    
    
    func webView(_ webView: WKWebView, didFinish navigation: WKNavigation!) {
        activityIndicator.stopAnimating()        
    }
    
    func webView(_ webView: WKWebView, didFail navigation: WKNavigation!, withError error: Error) {
        activityIndicator.stopAnimating()
    }
    
}
