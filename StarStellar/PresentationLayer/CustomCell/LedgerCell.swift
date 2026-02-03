//
//  LedgerCell.swift
//  StarStellar
//
//  Created by Apple on 08/08/19.
//  Copyright © 2019 Apple. All rights reserved.
//

import UIKit

class LedgerCell: UITableViewCell {

    @IBOutlet weak var lblDate: UILabel!
    @IBOutlet weak var lblDescription: UILabel!
    @IBOutlet weak var lblEarn: UILabel!
    @IBOutlet weak var lblReedem: UILabel!
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }
    
}
