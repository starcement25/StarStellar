//
//  TEPendingEngineerCell.swift
//  StarStellar
//
//  Created by Apple on 15/11/19.
//  Copyright © 2019 Apple. All rights reserved.
//

import UIKit

class TEPendingEngineerCell: UITableViewCell {

    @IBOutlet weak var imgViewEngineer: UIImageView!
    @IBOutlet weak var lblEngineerName: UILabel!
    @IBOutlet weak var lblEngineerMobile: UILabel!
    @IBOutlet weak var btnApprove: FPButton!
    @IBOutlet weak var btnReject: FPButton!
    var strEngineerId = ""
    
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }
    
}
