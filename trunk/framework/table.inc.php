<?php
/*
* This file is part of the xanthin+ project.
*
* Copyright (C) 2006  Mario Casciaro <xshadow [at] email (dot) it>
*
* Licensed under: 
*   - Apache License, Version 2.0 or
*   - GNU General Public License (GPL)
* You should have received at least one copy of them along with this program.
*
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" 
* AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, 
* THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR 
* PURPOSE ARE DISCLAIMED.SEE YOUR CHOOSEN LICENSE FOR MORE DETAILS.
*/



/**
 * The xTable is used to display and edit regular two-dimensional tables of cells.
 */
class xTable extends xElement
{
	var $m_model;
	var $m_css_class;
	var $m_path;
	
	function xTable($path,$table_model,$css_class)
	{
		$this->xElement();
		
		$this->m_path = $path;
		$this->m_model = $table_model;
		$this->m_css_class = $css_class;
	}
	
	/**
	 * @see xElement::render()
	 */
	function render()
	{
		$out = '<div class="'. $this->m_css_class . '"><table>';
		$column_count = $this->m_model->getColumnCount();
		$row_count = $this->m_model->getRowCount();
		
		$out .= '<tr>';
		for($i = 0;$i < $column_count;$i++)  //header
		{
			$column = $this->m_model->getColumn($i);
			if(! $column->m_sortable)
				$out .= '<th>'.$column->m_label.'</th>';
			else
			{
				$p_copy = xanth_clone($this->m_path);
				$p_copy->m_params['order'] = $column->m_name;
				$direction = 'desc';
				if(isset($p_copy->m_params['direction']) && isset($p_copy->m_params['order']))
				{
					if($p_copy->m_params['order'] === $column->m_name && $p_copy->m_params['direction'] === 'desc')
						$direction = 'asc';
				}
				
				$p_copy->m_params['direction'] = $direction;
				
				$out .= '<th><a href="'.$p_copy->getLink().'">'.$column->m_label.'</a></th>';
			}
		}
		$out .= '</tr>';
		
		for($i = 0;$i < $row_count;$i++)  //contents
		{
			$odd_even = 'tr-odd';
			if($i % 2 == 0)
				$odd_even = 'tr-even';
				
			$out .= '<tr class="'.$odd_even.' '. $this->m_model->getRowClass($i) .'">';
			
			for($j = 0;$j < $column_count;$j++)
			{
				$column = $this->m_model->getColumn($j);
				$odd_even = 'td-odd';
					if($i % 2 == 0)
						$odd_even = 'td-even';
				
				$out .= '<td class="'.$odd_even.' '. $column->m_name .'">';
				$out .= $this->m_model->getValueAt($i,$j);
				$out .= '</td>';
			}
			
			$out .= '</tr>';
		}
		$out .= '</table></div>';
		
		return $out;
	}
}



/**
 * Represent a table column
 */
class xColumn
{
	var $m_label;
	var $m_name;
	var $m_sortable;
	
	function xColumn($label,$name,$sortable)
	{
		$this->m_label = $label;
		$this->m_name = $name;
		$this->m_sortable = $sortable;
	}
}



/**
 * @abstract
 */
class xAbstractTableModel
{
	function xTableModel()
	{
	}
	
	/**
	 * Returns the header name of the specified column
	 * @param int $col
	 * @return xColumn
	 */
	function getColumn($col)
	{
	}

	/**
	 * Returns the number of rows to be rendered
	 * @return int
	 */
    function getRowCount()
	{   
	}
    
    /**
     * Returns 
     */
	function getColumnCount() 
	{
	}
	
	
    function getValueAt($row, $col)
	{
	}
	
	
	function getRowClass($row)
	{
	}
}



/**
 *
 */
class xDefaultTableModel extends xAbstractTableModel
{
	var $m_columns;
	var $m_data;
	var $m_row_css_classes;
	
	/**
	 * 
	 */
	function xDefaultTableModel($columns,$data,$row_css_classes = NULL)
	{
		$this->m_columns = $columns;
		$this->m_data = $data;
		
		if(!empty($row_css_classes))
			$this->m_row_css_classes = $row_css_classes;
		else
			$row_css_classes = array_fill (0,count($data),'');
	}
	
	/**
	 * @see xAbstractTableModel::getColumn()
	 */
	function getColumn($col)
	{
		return $this->m_columns[$col];
	}

	/**
	 * @see xAbstractTableModel::getRowCount()
	 */
    function getRowCount()
	{
		return count($this->m_data);
	}
    
    /**
     * @see xAbstractTableModel::getColumnCount() 
     */
	function getColumnCount() 
	{
		return count($this->m_columns);
	}
	
	/**
     * @see xAbstractTableModel::getValueAt() 
     */
    function getValueAt($row, $col)
	{
		return $this->m_data[$row][$col];
	}
	
	/**
     * @see xAbstractTableModel::getRowClass() 
     */
	function getRowClass($row)
	{
		return $this->m_row_css_classes[$row];
	}
}
?>