<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	 xmlns:set="http://exslt.org/sets"
	 xmlns:date="http://exslt.org/date">
	

	<xsl:param name="homecountry">ES</xsl:param>
	<xsl:decimal-format name="euros" decimal-separator="," grouping-separator="." /> 
	<xsl:decimal-format name="pounds" decimal-separator="." grouping-separator="," /> 

	<xsl:output method='html' />

	<xsl:template match='text()' />

	<xsl:template match="invoices">
		<xsl:apply-templates select="invoice"/>
	</xsl:template>

	<xsl:template match="invoice">
		<div class="header">
			<div id='logo'>
				<img src="../images/schoollogo.png" width="130px"/>
			</div>
			<div class="right" style="font-size:7pt;margin-right:5%;border-style:solid;
border-width:1px;padding:1px;">
				SCHOOL INVOICE
			</div>
		</div>

		<div class="spacer"></div>
		<!--div class="spacer"></div-->

		<div class="center" style="overflow:hiden;display:block;">
			<table>
				<tr><td colspan="2" style="padding:1em;"></td></tr>
				<tr class="borderleft bordertop borderright">
					<td class="bold" style="">DATE</td>
					<td class="bold borderleft" style="">INVOICE NO.</td>
				</tr>
				<tr class="borderleft borderbottom borderright">
					<td>
						<xsl:call-template name="FormatDate">
							<xsl:with-param name="date" select="issuedate/value/text()"/>
						</xsl:call-template>
					</td>
					<td class="borderleft"><xsl:value-of select="reference/value" /></td>
				</tr>
				<tr><td colspan="2" style="padding:0.7em;"></td></tr>
				<tr class="borderleft bordertop borderright">
					<td class="bold">STUDENT NAME</td>
					<td class="bold borderleft">CLASS</td>
				</tr>
				<tr class="borderleft borderbottom borderright">
					<td><xsl:value-of select="studentname/value" />&#160;</td>
					<td class="borderleft"><xsl:value-of select="registrationgroup/value" /></td>
				</tr>
				<tr class="borderleft bordertop borderright">
					<td class="bold" colspan="2"><xsl:value-of select="remittancename/value" /></td>
				</tr>
				<xsl:apply-templates select="charges/charge" />
				<tr class="borderleft borderright"><td colspan="2" style="padding:0.7em;"></td></tr>
				<tr class="borderleft borderbottom borderright">
					<td class="bold">AMOUNT</td>
					<td class="bold" style="text-align:right;"><xsl:value-of select="format-number(totalamount/value,'###.###,00','euros')" />&#160;&#8364;</td>
				</tr>
				<tr><td colspan="2" style="padding:0.7em;"></td></tr>
				<tr class="borderleft bordertop borderright">
					<td class="bold" style="">PAYMENT TYPE</td>
					<td class="bold borderleft" style="">V.A.</td>
				</tr>
				<tr class="borderleft borderbottom borderright">
					<td>
						<xsl:choose>
							<xsl:when test="paymenttype/value/text()='bank'">
								BANK
							</xsl:when>
							<xsl:when test="paymenttype/value/text()='cash'">
								CASH
							</xsl:when>
							<xsl:when test="paymenttype/value/text()='other'">
								OTHER
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="paymenttype/value" />
							</xsl:otherwise>
						</xsl:choose>
					</td>
					<td class="borderleft"></td>
				</tr>
				<tr><td colspan="2" style="padding:1em;"></td></tr>
			</table>
		</div>

		<div class="spacer"></div>

		<div class="cuthere">
		</div>

		<div class="spacer"></div>
		<div class="extraspacer"></div>

		<div class="header">
			<div id='logo'>
				<img src="../images/schoollogo.png" width="130px"/>
			</div>
			<div class="right" style="font-size:7pt;margin-right:5%;border-style:solid;
border-width:1px;padding:1px;">
				SCHOOL
			</div>
		</div>

		<div class="spacer"></div>
		<!--div class="spacer"></div-->

		<div class="center" style="overflow:hiden;display:block;">
			<table>
				<tr><td colspan="2" style="padding:0.7em;"></td></tr>
				<tr class="borderleft bordertop borderbottom borderright">
					<td class="bold" style="">ADDRESS</td>
					<td colspan="2" style="text-align:right;">SCHOOL</td>
				</tr>
				<tr class="borderleft borderright">
					<td class="bold borderleft">RECEIPT NO.</td>
					<td class="bold borderleft">ISSUE DATE</td>
					<td class="bold borderleft">PAYMENT DATE</td>
				</tr>
				<tr class="borderleft borderbottom borderright">
					<td class="borderleft"><xsl:value-of select="reference/value" /></td>
					<td class="borderleft">
						<xsl:call-template name="FormatDate">
							<xsl:with-param name="date" select="issuedate/value/text()"/>
						</xsl:call-template>
					</td>
					<td class="borderleft">
						<xsl:choose>
							<xsl:when test="paymenttype/value/text()='bank'">
								<xsl:call-template name="FormatDate">
									<xsl:with-param name="date" select="paymentdate/value/text()"/>
								</xsl:call-template>
							</xsl:when>
							<xsl:otherwise>
								AT SIGHT
							</xsl:otherwise>
						</xsl:choose>
					</td>
				</tr>
				<tr><td colspan="3" style="padding:0.7em;"></td></tr>
			</table>
			<table>
				<tr class="borderleft bordertop borderright">
					<td class="bold">STUDENT NAME</td>
					<td class="bold borderleft">CLASS</td>
				</tr>
				<tr class="borderleft borderbottom borderright">
					<td><xsl:value-of select="studentname/value" />&#160;</td>
					<td class="borderleft"><xsl:value-of select="registrationgroup/value" /></td>
				</tr>
				<tr class="borderleft bordertop borderright">
					<td class="bold" colspan="2"><xsl:value-of select="remittancename/value" /></td>
				</tr>
				<xsl:apply-templates select="charges/charge" />
				<tr class="borderleft borderright"><td colspan="2" style="padding:0.7em;"></td></tr>
				<tr class="borderleft borderbottom borderright">
					<td class="bold">AMOUNT</td>
					<td class="bold" style="text-align:right;"><xsl:value-of select="format-number(totalamount/value,'###.###,00','euros')" />&#160;&#8364;</td>
				</tr>
				<tr><td colspan="2" style="padding:0.7em;"></td></tr>
				<tr class="borderleft bordertop borderbottom borderright">
					<td class="bold" style="">PAYMENT TYPE</td>
					<td>
						<xsl:choose>
							<xsl:when test="paymenttype/value/text()='bank'">
								BANK
							</xsl:when>
							<xsl:when test="paymenttype/value/text()='cash'">
								CASH
							</xsl:when>
							<xsl:when test="paymenttype/value/text()='other'">
								OTHER
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="paymenttype/value" />
							</xsl:otherwise>
						</xsl:choose>
					</td>
				</tr>
			</table>
		</div>


		<div class="spacer"></div>
		<!--div class="spacer"></div>
		<div class="spacer"></div-->
		<div class="newpage" style="page-break-after: always;">
			<xsl:choose>
				<xsl:when test="position() mod 3 = 0 and not(position() = last())">
					<hr />
				</xsl:when>
				<xsl:otherwise>
					<div class="center" style="overflow:hidden;display:block;height:55px;">
					</div>
					<div class="spacer"></div>
				</xsl:otherwise>
			</xsl:choose>
		</div>

	</xsl:template>



	<xsl:template match="charge">

		<xsl:variable name="amount">
		  <xsl:choose>
			<xsl:when test="amount/value!='' and amount/value!=' '">
			  <xsl:value-of select="amount/value" />
			</xsl:when>
			<xsl:otherwise>
			  0
			</xsl:otherwise>
		  </xsl:choose>
		</xsl:variable>
		<tr class="borderleft borderright">
			<td>
				<xsl:value-of select="detail/value" />&#160;
			</td>
			<td style="text-align:right;">
				<xsl:value-of select="format-number($amount,'###.##0,00','euros')" />&#160;&#8364;
			</td>
		</tr>

	</xsl:template>

<!-- START FormatDate definition & structure -->
<!-- expected date format 2001-12-31 -->
<!-- new date format 31. Dezember 2001 -->
<xsl:template name="FormatDate">
  <xsl:param name="date" />

  <xsl:variable name="year">
    <xsl:value-of select="substring($date,1,4)" />
  </xsl:variable>
  <xsl:variable name="month-temp">
    <xsl:value-of select="substring-after($date,'-')" />
  </xsl:variable>
  <xsl:variable name="month">
    <xsl:value-of select="substring-before($month-temp,'-')" />
  </xsl:variable>
  <xsl:variable name="day-temp">
    <xsl:value-of select="substring-after($month-temp,'-')" />
  </xsl:variable>
  <xsl:variable name="day">
    <xsl:value-of select="substring($day-temp,1,2)" />
  </xsl:variable>
  

  <!-- write day -->
  <xsl:choose>
    <xsl:when test="$day &lt; 10"><xsl:value-of select="substring($day, string-length($day))"/></xsl:when>
    <xsl:otherwise ><xsl:value-of select="$day"/></xsl:otherwise>
  </xsl:choose>

  <!--xsl:choose>
    <xsl:when test="$day='01'">st </xsl:when>
    <xsl:when test="$day='02'">nd </xsl:when>
    <xsl:when test="$day='03'">rd </xsl:when>
    <xsl:when test="$day &gt; 20 and substring($day, string-length($day))='1'">st </xsl:when>
    <xsl:when test="$day &gt; 20 and substring($day, string-length($day))='2'">nd </xsl:when>
    <xsl:when test="$day &gt; 20 and substring($day, string-length($day))='3'">rd </xsl:when>
    <xsl:otherwise >th</xsl:otherwise>
  </xsl:choose-->
  <xsl:value-of select="' '"/>
  
  <!-- do month substring replace, write -->
  <xsl:choose>
    <xsl:when test="$month = '1' or $month = '01'">January</xsl:when>
    <xsl:when test="$month = '2' or $month = '02'">February</xsl:when>
    <xsl:when test="$month = '3' or $month = '03'">March</xsl:when>
    <xsl:when test="$month = '4' or $month = '04'">April</xsl:when>
    <xsl:when test="$month = '5' or $month = '05'">May</xsl:when>
    <xsl:when test="$month = '6' or $month = '06'">June</xsl:when>
    <xsl:when test="$month = '7' or $month = '07'">July</xsl:when>
    <xsl:when test="$month = '8' or $month = '08'">August</xsl:when>
    <xsl:when test="$month = '9' or $month = '09'">September</xsl:when>
    <xsl:when test="$month = '10'">October</xsl:when>
    <xsl:when test="$month = '11'">November</xsl:when>
    <xsl:when test="$month = '12'">December</xsl:when>
  </xsl:choose>

  <!-- write space & year -->
  <xsl:value-of select="' '"/>
  <xsl:value-of select="$year"/>
  
</xsl:template>
<!-- END FormatDate definition & structure -->

</xsl:stylesheet>
