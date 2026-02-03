//
//  MySiteRecommPendingCell.swift
//  StarStellar
//
//  Created by Forcepower Infotech Pvt Ltd on 02/01/24.
//  Copyright © 2024 Apple. All rights reserved.
//

import UIKit

class MySiteRecommPendingCell: UITableViewCell {
    
    @IBOutlet weak var lblSiteName: UILabel!
    @IBOutlet weak var lblSubmissionDate: UILabel!
    @IBOutlet weak var viewStatus: FPView!
    @IBOutlet weak var lblMobile: UILabel!

    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }
    
}
