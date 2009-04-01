<xsl:stylesheet version='1.0' xmlns:xsl='http://www.w3.org/1999/XSL/Transform'>
<!-- xslt for subject reports presented in a table with with one to several 
	 assessment grades, a second additional table displays (optional) 
	 short written comments -->


<xsl:output method='html' />

<xsl:template match='text()' />
  <xsl:param name="homecountry">ES</xsl:param>
  <xsl:decimal-format name="euros" decimal-separator="," grouping-separator="." /> 
  <xsl:decimal-format name="pounds" decimal-separator="." grouping-separator="," /> 

<xsl:template match="html/body/div">
  <xsl:apply-templates select="order" />
</xsl:template>

<xsl:template match="order">
  <div id='logo'>
	<img src="../images/schoollogo.png" width="160px"/>
  </div>

  <div class="right" style="border:solid 2px #bbb;padding:4px;">

	<h1 style="font-size:11pt;width:100%;background-color:#eee;">
	  <xsl:value-of select="supplier/name/value/text()" />&#160;
	</h1>

	<xsl:apply-templates select="supplier/address" />

  </div>

  <div class='spacer'></div>

  <div class="center">
	<table>
	  <tr>
		<th>
		  Order Number
		</th>
		<th>
		  Date Placed
		</th>
	  </tr>
	  <tr>
		<td>
		  <div><xsl:value-of select="reference/value/text()" />&#160;</div>
		</td>
		<td>
		  <div><xsl:value-of select="date/value/text()" />&#160;</div>
		</td>
	  </tr>
	</table>
  </div>

  <div class='bigspacer'></div>

  <div class="center">
	<table>
	  <tr>
		<th>
		  Item Description
		</th>
		<th>
		  Reference
		</th>
		<th>
		  Quantity
		</th>
		<th>
		  Cost (
		  <xsl:choose>
			<xsl:when test="currency/value/text()='0'">
			  EUR
			</xsl:when>
			<xsl:when test="currency/value/text()='1'">
			  GBP
			</xsl:when>
		  </xsl:choose>
				)
		</th>
	  </tr>
	  <xsl:apply-templates select="materials/material" />
	  <tr>
		<th colspan="4">
		</th>
	  </tr>
	  <tr>
		<td colspan="2"></td>
		<td>Total Cost</td>
		<td>
		  <div>

			<xsl:call-template name="costsum">
			  <xsl:with-param name="index" select="1"/>
			  <xsl:with-param name="maxindex" select="count(materials/material)+1"/>
			  <xsl:with-param name="totalcost" select="0"/>
			</xsl:call-template>

			&#160;
		  </div>
		</td>
	  </tr>
	  <tr>
		<th colspan="4">
		</th>
	  </tr>

	</table>
  </div>



<div class='spacer'></div>

<hr />

</xsl:template>



<xsl:template match="material">
  <xsl:variable name="cost">
	<xsl:value-of select="quantity/value * unitcost/value"/>
  </xsl:variable>

	  <tr>
		<td>
		  <div>
			<xsl:value-of select="detail/value/text()" />&#160;
		  </div>
		</td>
		<td>
		<div>
		  <xsl:value-of select="supplierreference/value/text()" />&#160;
		</div>
		</td>
		<td>
		<div>
		  <xsl:value-of select="quantity/value/text()" />&#160;
		</div>
		</td>
		<td>
		<div>

		  <xsl:choose>
			<xsl:when test="../../currency/value/text()='0'">
			  <xsl:value-of select="format-number($cost,'###.###,00','euros')" />
			</xsl:when>
			<xsl:when test="../../currency/value/text()='1'">
			  <xsl:value-of select="format-number($cost,'###,###.00','pounds')" />
			</xsl:when>
		  </xsl:choose>

		</div>
		</td>
	  </tr>

</xsl:template>



<xsl:template match="address">

  <div>
	<table class="address">
	  <xsl:if test="street/value/text()!=' '">
		<tr>
		  <td>
			<xsl:value-of select="street/value/text()" />&#160;
		  </td>
		</tr>
	  </xsl:if>
	  <xsl:if test="neighbourhood/value/text()!=' '">
		<tr>
		  <td>
			<xsl:value-of select="neighbourhood/value/text()" />&#160;
		  </td>
		</tr>
	  </xsl:if>
	  <xsl:if test="(town/value/text()!=' ' or postcode/value/text()!='') and country/value/text()!='GB'">
		<tr>
		  <td>
			<xsl:value-of select="postcode/value/text()" />&#160;
			<xsl:value-of select="town/value/text()" />&#160;
		  </td>
		</tr>
	  </xsl:if>
	  <xsl:if test="postcode/value/text()!=' ' and country/value/text()='GB'">
		<tr>
		  <td>
			<xsl:value-of select="town/value/text()" />&#160;
		  </td>
		</tr>
		<tr>
		  <td>
			<xsl:value-of select="postcode/value/text()" />&#160;
		  </td>
		</tr>
	  </xsl:if>
	  <xsl:if test="country/value_display/text()!=' ' and country/value/text()!=$homecountry">
		<tr>
		  <td>
			<xsl:value-of select="country/value_display/text()" />&#160;
		  </td>
		</tr>
	  </xsl:if>
	</table>
  </div>

</xsl:template>


<xsl:template name="costsum">

  <xsl:param name="index">1</xsl:param>
  <xsl:param name="maxindex">1</xsl:param>
  <xsl:param name="totalcost">0</xsl:param>

  <xsl:variable name="cost">
	<xsl:value-of select="materials/material[$index]/quantity/value * materials/material[$index]/unitcost/value"/>
  </xsl:variable>

  <xsl:if test="$index &lt; $maxindex">
    <xsl:call-template name="costsum">
	  <xsl:with-param name="index" select="$index + 1"/>
	  <xsl:with-param name="maxindex" select="$maxindex"/>
	  <xsl:with-param name="totalcost" select="$totalcost + $cost"/>
	</xsl:call-template>
  </xsl:if>

  <xsl:if test="$index=$maxindex">
		  <xsl:choose>
			<xsl:when test="currency/value/text()='0'">
			  <xsl:value-of select="format-number($totalcost,'###.###,00 &#x20AC;','euros')" />
			</xsl:when>
			<xsl:when test="currency/value/text()='1'">
			  <xsl:value-of select="format-number($totalcost,'&#163; ###,###.00','pounds')" />
			</xsl:when>
		  </xsl:choose>
  </xsl:if>


</xsl:template>


</xsl:stylesheet>
