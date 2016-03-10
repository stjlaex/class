<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"  xmlns:set="http://exslt.org/sets">

  <xsl:param name="homecountry">ES</xsl:param>
  <xsl:decimal-format name="euros" decimal-separator="," grouping-separator="." /> 
  <xsl:decimal-format name="pounds" decimal-separator="." grouping-separator="," /> 

  <xsl:output method='html' />

<xsl:template match='text()' />

<xsl:template match="invoices">
  <xsl:apply-templates select="invoice"/>
</xsl:template>

<xsl:template match="invoice">

  <div class="spacer"></div>
  <div class="spacer"></div>
  <div class="center" style="overflow:hiden;display:block;height:70px;">
	<div class="left" style="width:100px;text-align:center;">
	  <xsl:value-of select="reference/value" />
	</div>
	<div class="left" style="width:280px;text-align:center;">
	  ADDRESS
	</div>
	<div  class="left" style="width:70px;text-align:right;">
	  <xsl:value-of select="format-number(totalamount/value,'###.###,00','euros')" />
	</div>

	<div class="spacer"></div>
	<div class="spacer"></div>
	<div class="spacer"></div>

	<div class="left" style="width:210px;text-align:center;">
	  <xsl:value-of select="issuedate/value" />
	</div>
	<div class="right" style="width:210px;text-align:center;">
	  <xsl:choose>
		<xsl:when test="paymenttype/value='1'">
		  <xsl:value-of select="paymentdate/value" />&#160;
		</xsl:when>
		<xsl:otherwise>
		  AT SIGHT
		</xsl:otherwise>
	  </xsl:choose>
	</div>
  </div>

  <div class="spacer"></div>
  <div class="spacer"></div>
  <div class="spacer"></div>
  <div class="spacer"></div>

  <div class="center" style="overflow:hiden;display:block;height:100px;">

	<xsl:value-of select="studentname/value" />&#160;

	<table>
	  <xsl:apply-templates select="charges/charge" />
	</table>
  </div>

  <div class="spacer"></div>

  <div class="center" style="overflow:hiden;display:block;height:100px;">
	<div class="left">
	  <table class="address">
		<tr>
		  <td>
			<xsl:value-of select="accountname/value" />
		  </td>
		</tr>
		<xsl:apply-templates select="address" />
	  </table>
	</div>
  </div>

  <div class="spacer"></div>
  <xsl:choose>
	<xsl:when test="position() mod 3 = 0 and not(position() = last())">
	  <hr />
	</xsl:when>
	<xsl:otherwise>
	  <div class="center" style="overflow:hiden;display:block;height:55px;">
	  </div>
	  <div class="spacer"></div>
	</xsl:otherwise>
  </xsl:choose>


</xsl:template>



<xsl:template match="charge">

  <tr>
	<td>
	  <xsl:value-of select="detail/value" />&#160;
	</td>
	<td>
	  <xsl:value-of select="format-number(amount/value,'###.###,00','euros')" />
	</td>
  </tr>

</xsl:template>


<!--
<xsl:template match="address">
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
	  <xsl:if test="(town/value/text()!=' ' or postcode/value/text()!='')">
		<tr>
		  <td>
			<xsl:value-of select="postcode/value/text()" />&#160;
			<xsl:value-of select="town/value/text()" />&#160;
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
</xsl:template>
-->


<xsl:template match="address">
		<tr>
		  <td>
			<xsl:value-of select="street/value/text()" />&#160;
		  </td>
		</tr>
		<tr>
		  <td>
			<xsl:value-of select="neighbourhood/value/text()" />&#160;
		  </td>
		</tr>
		<tr>
		  <td>
			<xsl:value-of select="postcode/value/text()" />&#160;
			<xsl:value-of select="town/value/text()" />&#160;
		  </td>
		</tr>
	  <xsl:if test="country/value_display/text()!=' ' and country/value/text()!=$homecountry">
		<tr>
		  <td>
			<xsl:value-of select="country/value_display/text()" />&#160;
		  </td>
		</tr>
	  </xsl:if>
</xsl:template>

</xsl:stylesheet>
