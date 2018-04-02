<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\base\DynamicModel;

use app\models\Invoices;
use app\models\Purchases;
use app\models\Estimations;
use app\components\Helper;

/**
 * ReportsController implements the CRUD actions for Reports model.
 */
class ReportsController extends Controller
{
    
    public function actionIndex()
    {
        $model = new DynamicModel(['date_range', 'type']);
		$model->addRule(['date_range','type'], 'required');
	 
		if($model->load(Yii::$app->request->post())){
			$dates = explode("-",$model->date_range);
			
			if($model->type=="sales")
			{
				$sales = Invoices::find()
								->JoinWith('invoiceItems')
								->where(['between', 'invoiceDate', trim($dates[0]), trim($dates[1])])
								->orderBy(['invoiceDate' => SORT_ASC])
								->all();
				$this->salesExport($sales,$model->date_range);
			}
			if($model->type=="purchase")
			{
				$purchases = Purchases::find()
								->JoinWith('purchaseItems')
								->where(['between', 'purchaseDate', trim($dates[0]), trim($dates[1])])
								->orderBy(['purchaseDate' => SORT_ASC])
								->all();
				$this->purchasesExport($purchases,$model->date_range);
			}
			if($model->type=="estimation")
			{
				$estimations = Estimations::find()
								->JoinWith('estimationItems')
								->where(['between', 'estimationDate', trim($dates[0]), trim($dates[1])])
								->orderBy(['estimationDate' => SORT_ASC])
								->all();
				$this->estimationsExport($estimations,$model->date_range);
			}
										
			//return $this->redirect(['index']);
		}
        return $this->render('index', ['model'=>$model]);
    }
    
    
    public function salesExport($sales,$date_range)
    {
		
		$objPHPExcel = new \PHPExcel();
		$sheet=0;
		 $objPHPExcel->setActiveSheetIndex($sheet);
		 
			$common_styles = array(
				'font'  => array(
					//'bold'  => true,
					'size'  => 8,
					'name'  => 'Verdana'
				));
							
			$special_styles = array(
				'font'  => array(
					'bold'  => true,
					'size'  => 8,
					'name'  => 'Verdana'
				));	
						
			$bottom_styles = array(
				'font'  => array(
					'bold'  => true,
					'size'  => 10,
					'name'  => 'Verdana'
				));			
			
			foreach(range('A','J') as $columnID) {
				$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
				$objPHPExcel->getActiveSheet()->getStyle($columnID)->applyFromArray($common_styles);	
		   }
		   
		   $objPHPExcel->getActiveSheet()->getStyle("A1:C1")->applyFromArray($special_styles);
		   $objPHPExcel->getActiveSheet()->getStyle("A3:J3")->applyFromArray($special_styles);
		   $objPHPExcel->getActiveSheet()->getStyle('C1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		   
		    
		    $objPHPExcel->getActiveSheet()->mergeCells('C1:J1');    
		    $objPHPExcel->getActiveSheet()->mergeCells('A1:B1');    
		    $objPHPExcel->getActiveSheet()->setTitle("Sales")
					 ->setCellValue('A1', 'Ledger : Sales Account')
					 ->setCellValue('C1', $date_range);
			
			
			$objPHPExcel->getActiveSheet()->setCellValue('A3','Date');	 
			$objPHPExcel->getActiveSheet()->setCellValue('B3','Customer');
			$objPHPExcel->getActiveSheet()->setCellValue('C3','Customer GSTIN');
			$objPHPExcel->getActiveSheet()->setCellValue('D3','Voucher Type');
			$objPHPExcel->getActiveSheet()->setCellValue('E3','Voucher No');
			$objPHPExcel->getActiveSheet()->setCellValue('F3','Gross Total');
			$objPHPExcel->getActiveSheet()->setCellValue('G3','Sales Amount');
			$objPHPExcel->getActiveSheet()->setCellValue('H3','CGST');
			$objPHPExcel->getActiveSheet()->setCellValue('I3','SGST');
			$objPHPExcel->getActiveSheet()->setCellValue('J3','IGST');
					
		
		$row=4;				
		$netTotal = 0;
		$subTotal = 0;
		$cgstTotal = 0;
		$sgstTotal = 0;
		$igstTotal = 0;
		foreach ($sales as $sale) { 
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,$sale->invoiceDate); 
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$row,$sale->customerName);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$row,$sale->customerGstin);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$row,"Sales");
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$row,$sale->invoiceNo);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$row,Helper::amount_to_money($sale->netTotal));
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$row,Helper::amount_to_money($sale->subTotal));
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$row,Helper::amount_to_money($sale->cgstTotal));
			$objPHPExcel->getActiveSheet()->setCellValue('i'.$row,Helper::amount_to_money($sale->sgstTotal));
			$objPHPExcel->getActiveSheet()->setCellValue('j'.$row,Helper::amount_to_money($sale->igstTotal));
			
			$objPHPExcel->getActiveSheet()->getStyle('F'.$row.":J".$row)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			
			$objPHPExcel->getActiveSheet()->getStyle('B'.$row)->applyFromArray($special_styles);
			$objPHPExcel->getActiveSheet()->getStyle('F'.$row)->applyFromArray($special_styles);
			
			$netTotal = $netTotal + $sale->netTotal;
			$subTotal = $subTotal + $sale->subTotal;
			$cgstTotal = $cgstTotal + $sale->cgstTotal;
			$sgstTotal = $sgstTotal + $sale->sgstTotal;
			$igstTotal = $igstTotal + $sale->igstTotal;
			
			$row++;
		}
		
		
		$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(15);
		
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$row,Helper::amount_to_money($netTotal));
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$row,Helper::amount_to_money($subTotal));
		$objPHPExcel->getActiveSheet()->setCellValue('H'.$row,Helper::amount_to_money($cgstTotal));
		$objPHPExcel->getActiveSheet()->setCellValue('i'.$row,Helper::amount_to_money($sgstTotal));
		$objPHPExcel->getActiveSheet()->setCellValue('j'.$row,Helper::amount_to_money($igstTotal));
		
		$objPHPExcel->getActiveSheet()->getStyle('F'.$row.":J".$row)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		
		$objPHPExcel->getActiveSheet()->getStyle('F'.$row.":J".$row)->applyFromArray($bottom_styles);
		
		
		header('Content-Type: application/vnd.ms-excel');
		$filename = "sales_".date("d-m-Y-His").".xls";
		header('Content-Disposition: attachment;filename='.$filename .' ');
		header('Cache-Control: max-age=0');
		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit();  
	}
	public function purchasesExport($purchases,$date_range)
    {
		
		$objPHPExcel = new \PHPExcel();
		$sheet=0;
		 $objPHPExcel->setActiveSheetIndex($sheet);
		 
			$common_styles = array(
				'font'  => array(
					//'bold'  => true,
					'size'  => 8,
					'name'  => 'Verdana'
				));
							
			$special_styles = array(
				'font'  => array(
					'bold'  => true,
					'size'  => 8,
					'name'  => 'Verdana'
				));	
						
			$bottom_styles = array(
				'font'  => array(
					'bold'  => true,
					'size'  => 10,
					'name'  => 'Verdana'
				));			
			
			foreach(range('A','J') as $columnID) {
				$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
				$objPHPExcel->getActiveSheet()->getStyle($columnID)->applyFromArray($common_styles);	
		   }
		   
		   $objPHPExcel->getActiveSheet()->getStyle("A1:C1")->applyFromArray($special_styles);
		   $objPHPExcel->getActiveSheet()->getStyle("A3:J3")->applyFromArray($special_styles);
		   $objPHPExcel->getActiveSheet()->getStyle('C1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		   
		    
		    $objPHPExcel->getActiveSheet()->mergeCells('C1:J1');    
		    $objPHPExcel->getActiveSheet()->mergeCells('A1:B1');    
		    $objPHPExcel->getActiveSheet()->setTitle("Purchases")
					 ->setCellValue('A1', 'Ledger : Purchases Account')
					 ->setCellValue('C1', $date_range);
			
			
			$objPHPExcel->getActiveSheet()->setCellValue('A3','Date');	 
			$objPHPExcel->getActiveSheet()->setCellValue('B3','Supplier');
			$objPHPExcel->getActiveSheet()->setCellValue('C3','Supplier GSTIN');
			$objPHPExcel->getActiveSheet()->setCellValue('D3','Voucher Type');
			$objPHPExcel->getActiveSheet()->setCellValue('E3','Voucher No');
			$objPHPExcel->getActiveSheet()->setCellValue('F3','Gross Total');
			$objPHPExcel->getActiveSheet()->setCellValue('G3','Purchases Amount');
			$objPHPExcel->getActiveSheet()->setCellValue('H3','CGST');
			$objPHPExcel->getActiveSheet()->setCellValue('I3','SGST');
			$objPHPExcel->getActiveSheet()->setCellValue('J3','IGST');
					
		
		$row=4;				
		$netTotal = 0;
		$subTotal = 0;
		$cgstTotal = 0;
		$sgstTotal = 0;
		$igstTotal = 0;
		foreach ($purchases as $purchase) { 
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,$purchase->purchaseDate); 
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$row,$purchase->supplierName);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$row,$purchase->supplierGstin);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$row,"Purchases");
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$row,$purchase->purchaseNo);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$row,Helper::amount_to_money($purchase->netTotal));
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$row,Helper::amount_to_money($purchase->subTotal));
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$row,Helper::amount_to_money($purchase->cgstTotal));
			$objPHPExcel->getActiveSheet()->setCellValue('i'.$row,Helper::amount_to_money($purchase->sgstTotal));
			$objPHPExcel->getActiveSheet()->setCellValue('j'.$row,Helper::amount_to_money($purchase->igstTotal));
			
			$objPHPExcel->getActiveSheet()->getStyle('F'.$row.":J".$row)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			
			$objPHPExcel->getActiveSheet()->getStyle('B'.$row)->applyFromArray($special_styles);
			$objPHPExcel->getActiveSheet()->getStyle('F'.$row)->applyFromArray($special_styles);
			
			$netTotal = $netTotal + $purchase->netTotal;
			$subTotal = $subTotal + $purchase->subTotal;
			$cgstTotal = $cgstTotal + $purchase->cgstTotal;
			$sgstTotal = $sgstTotal + $purchase->sgstTotal;
			$igstTotal = $igstTotal + $purchase->igstTotal;
			
			$row++;
		}
		
		
		$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(15);
		
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$row,Helper::amount_to_money($netTotal));
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$row,Helper::amount_to_money($subTotal));
		$objPHPExcel->getActiveSheet()->setCellValue('H'.$row,Helper::amount_to_money($cgstTotal));
		$objPHPExcel->getActiveSheet()->setCellValue('i'.$row,Helper::amount_to_money($sgstTotal));
		$objPHPExcel->getActiveSheet()->setCellValue('j'.$row,Helper::amount_to_money($igstTotal));
		
		$objPHPExcel->getActiveSheet()->getStyle('F'.$row.":J".$row)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		
		$objPHPExcel->getActiveSheet()->getStyle('F'.$row.":J".$row)->applyFromArray($bottom_styles);
		
		
		header('Content-Type: application/vnd.ms-excel');
		$filename = "purchases_".date("d-m-Y-His").".xls";
		header('Content-Disposition: attachment;filename='.$filename .' ');
		header('Cache-Control: max-age=0');
		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit();  
	}
	public function estimationsExport($estimations,$date_range)
    {
		
		$objPHPExcel = new \PHPExcel();
		$sheet=0;
		 $objPHPExcel->setActiveSheetIndex($sheet);
		 
			$common_styles = array(
				'font'  => array(
					//'bold'  => true,
					'size'  => 8,
					'name'  => 'Verdana'
				));
							
			$special_styles = array(
				'font'  => array(
					'bold'  => true,
					'size'  => 8,
					'name'  => 'Verdana'
				));	
						
			$bottom_styles = array(
				'font'  => array(
					'bold'  => true,
					'size'  => 10,
					'name'  => 'Verdana'
				));			
			
			foreach(range('A','J') as $columnID) {
				$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
				$objPHPExcel->getActiveSheet()->getStyle($columnID)->applyFromArray($common_styles);	
		   }
		   
		   $objPHPExcel->getActiveSheet()->getStyle("A1:C1")->applyFromArray($special_styles);
		   $objPHPExcel->getActiveSheet()->getStyle("A3:J3")->applyFromArray($special_styles);
		   $objPHPExcel->getActiveSheet()->getStyle('C1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		   
		    
		    $objPHPExcel->getActiveSheet()->mergeCells('C1:G1');    
		    $objPHPExcel->getActiveSheet()->mergeCells('A1:B1');    
		    $objPHPExcel->getActiveSheet()->setTitle("Estimations")
					 ->setCellValue('A1', 'Ledger : Estimations Account')
					 ->setCellValue('C1', $date_range);
			
			
			$objPHPExcel->getActiveSheet()->setCellValue('A3','Date');	 
			$objPHPExcel->getActiveSheet()->setCellValue('B3','Customer');
			$objPHPExcel->getActiveSheet()->setCellValue('C3','Customer GSTIN');
			$objPHPExcel->getActiveSheet()->setCellValue('D3','Voucher Type');
			$objPHPExcel->getActiveSheet()->setCellValue('E3','Voucher No');
			$objPHPExcel->getActiveSheet()->setCellValue('F3','Gross Total');
			$objPHPExcel->getActiveSheet()->setCellValue('G3','Estimations Amount');
			
					
		
		$row=4;				
		$netTotal = 0;
		$subTotal = 0;
		$cgstTotal = 0;
		$sgstTotal = 0;
		$igstTotal = 0;
		foreach ($estimations as $estimation) { 
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,$estimation->estimationDate); 
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$row,$estimation->customerName);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$row,$estimation->customerGstin);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$row,"Estimations");
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$row,$estimation->estimationNo);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$row,Helper::amount_to_money($estimation->netTotal));
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$row,Helper::amount_to_money($estimation->subTotal));
			
			$objPHPExcel->getActiveSheet()->getStyle('F'.$row.":G".$row)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			
			$objPHPExcel->getActiveSheet()->getStyle('B'.$row)->applyFromArray($special_styles);
			$objPHPExcel->getActiveSheet()->getStyle('F'.$row)->applyFromArray($special_styles);
			
			$netTotal = $netTotal + $estimation->netTotal;
			$subTotal = $subTotal + $estimation->subTotal;
			
			$row++;
		}
		
		
		$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(15);
		
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$row,Helper::amount_to_money($netTotal));
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$row,Helper::amount_to_money($subTotal));
		
		$objPHPExcel->getActiveSheet()->getStyle('F'.$row.":G".$row)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		
		$objPHPExcel->getActiveSheet()->getStyle('F'.$row.":G".$row)->applyFromArray($bottom_styles);
		
		
		header('Content-Type: application/vnd.ms-excel');
		$filename = "estimations_".date("d-m-Y-His").".xls";
		header('Content-Disposition: attachment;filename='.$filename .' ');
		header('Cache-Control: max-age=0');
		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit();  
	}

    
   
}
