<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"  xmlns:set="http://exslt.org/sets">

<!--  -->

<xsl:output method='html' />


<xsl:template match="stats">
  <xsl:variable name="years" select="set:distinct(stat/enrolyear/value)" />
  <xsl:variable name="dates" select="set:distinct(stat/date/value)" />

  <xsl:call-template name="appnext">
	<xsl:with-param name="years" select="$years"/>
	<xsl:with-param name="dates" select="$dates"/>
	<xsl:with-param name="cols" select="set:distinct(tables/table[name='appnext']/cols/col/value)"/>
  </xsl:call-template>

  <div class='spacer'></div>
  <div class='spacer'></div>

  <xsl:call-template name="enrolnext">
	<xsl:with-param name="years" select="$years"/>
	<xsl:with-param name="dates" select="$dates"/>
	<xsl:with-param name="cols" select="set:distinct(tables/table[name='enrolnext']/cols/col/value)"/>
  </xsl:call-template>

  <div class='spacer'></div>
  <hr />
  <div class='spacer'></div>

  <xsl:call-template name="appcurrent">
	<xsl:with-param name="years" select="$years"/>
	<xsl:with-param name="dates" select="$dates"/>
	<xsl:with-param name="cols" select="set:distinct(tables/table[name='appcurrent']/cols/col/value)"/>
  </xsl:call-template>

  <div class='spacer'></div>
  <div class='spacer'></div>

  <xsl:call-template name="enrolcurrent">
	<xsl:with-param name="years" select="$years"/>
	<xsl:with-param name="dates" select="$dates"/>
	<xsl:with-param name="cols" select="set:distinct(tables/table[name='enrolcurrent']/cols/col/value)"/>
  </xsl:call-template>

  <div class='spacer'></div>
  <div class='spacer'></div>
</xsl:template>




<xsl:template name="appnext">
  <xsl:param name="years"></xsl:param>
  <xsl:param name="dates"></xsl:param>
  <xsl:param name="cols"></xsl:param>

  <div class="head">
	<div class="left">
	  <label>Next Academic Year <xsl:value-of select="$years[2]" /> / <xsl:value-of select="$years[1]" /></label>
	</div>
	<div class="right">
	  <label>Applications  - <xsl:value-of select="school/value" /></label><xsl:text>&#160; </xsl:text>
	  <label><xsl:value-of select="datestamp" /></label>
	</div>
  </div>

  <div class="spacer"></div>

  <div>
	<table>
	  <tr>
		<th><label></label></th>
		<th colspan="1"><label>Enquiries</label></th>
		<th colspan="2"><label>Total Applications</label></th>
		<th colspan="7"><label>Applications in progress</label></th>
	  </tr>

	  <tr>
		<th><label></label></th>
		<xsl:call-template name="headerrow">
		  <xsl:with-param name="index" select="1"/>
		  <xsl:with-param name="cols" select="$cols"/>
		</xsl:call-template>
	  </tr>

	  <xsl:apply-templates select="stat[enrolyear/value=$years[1]][date/value=$dates[1]]/groups/group[type='year' or not(type)]">
		<xsl:with-param name="index" select="0"/>
		<xsl:with-param name="cols" select="$cols"/>
		<xsl:with-param name="yearnow" select="$years[1]"/>
		<xsl:with-param name="yearlast" select="$years[2]"/>
		<xsl:with-param name="dates" select="$dates"/>
	  </xsl:apply-templates>

	  <tr>
		<th><label></label></th>
		<xsl:call-template name="headerrow">
		  <xsl:with-param name="index" select="1"/>
		  <xsl:with-param name="cols" select="$cols"/>
		</xsl:call-template>
	  </tr>

	  <xsl:apply-templates select="stat[enrolyear/value=$years[1]][date/value=$dates[1]]/groups/group[type!='year']">
		<xsl:with-param name="index" select="0"/>
		<xsl:with-param name="cols" select="$cols"/>
		<xsl:with-param name="yearnow" select="$years[1]"/>
		<xsl:with-param name="yearlast" select="$years[2]"/>
		<xsl:with-param name="dates" select="$dates"/>
	  </xsl:apply-templates>

	  <tr>
		<th><label>TOTAL</label></th>
		<xsl:call-template name="totalrow">
		  <xsl:with-param name="index" select="1"/>
		  <xsl:with-param name="cols" select="$cols"/>
		  <xsl:with-param name="yearnow" select="$years[1]"/>
		  <xsl:with-param name="yearlast" select="$years[2]"/>
		  <xsl:with-param name="date1" select="$dates[1]"/>
		  <xsl:with-param name="date2" select="$dates[2]"/>
		</xsl:call-template>
	  </tr>

	</table>
  </div>


</xsl:template>





<xsl:template name="appcurrent">
  <xsl:param name="years"></xsl:param>
  <xsl:param name="dates"></xsl:param>
  <xsl:param name="cols"></xsl:param>

  <div class="head">
	<div class="left">
	  <label>Current Academic Year <xsl:value-of select="$years[3]" /> / <xsl:value-of select="$years[2]" /></label>
	</div>
	<div class="right">
	  <label>Applications  - <xsl:value-of select="school/value" /></label><xsl:text>&#160; </xsl:text>
	  <label><xsl:value-of select="datestamp" /></label>
	</div>
  </div>

  <div class="spacer"></div>

  <div>
	<table>
	  <tr>
		<th><label></label></th>
		<th colspan="2"><label>Total Applications</label></th>
		<th colspan="6"><label>Applications in progress</label></th>
	  </tr>

	  <tr>
		<th><label></label></th>
		<xsl:call-template name="headerrow">
		  <xsl:with-param name="index" select="1"/>
		  <xsl:with-param name="cols" select="$cols"/>
		</xsl:call-template>
	  </tr>

	  <xsl:apply-templates select="stat[enrolyear/value=$years[2]][date/value=$dates[1]]/groups/group[type='year' or not(type)]">
		<xsl:with-param name="index" select="0"/>
		<xsl:with-param name="cols" select="$cols"/>
		<xsl:with-param name="yearnow" select="$years[2]"/>
		<xsl:with-param name="yearlast" select="$years[3]"/>
		<xsl:with-param name="dates" select="$dates"/>
	  </xsl:apply-templates>

	  <tr>
		<th><label></label></th>
		<xsl:call-template name="headerrow">
		  <xsl:with-param name="index" select="1"/>
		  <xsl:with-param name="cols" select="$cols"/>
		</xsl:call-template>
	  </tr>

	  <xsl:apply-templates select="stat[enrolyear/value=$years[2]][date/value=$dates[1]]/groups/group[type!='year']">
		<xsl:with-param name="index" select="0"/>
		<xsl:with-param name="cols" select="$cols"/>
		<xsl:with-param name="yearnow" select="$years[2]"/>
		<xsl:with-param name="yearlast" select="$years[3]"/>
		<xsl:with-param name="dates" select="$dates"/>
	  </xsl:apply-templates>

	  <tr>
		<th><label>TOTAL</label></th>
		<xsl:call-template name="totalrow">
		  <xsl:with-param name="index" select="1"/>
		  <xsl:with-param name="cols" select="$cols"/>
		  <xsl:with-param name="yearnow" select="$years[2]"/>
		  <xsl:with-param name="yearlast" select="$years[3]"/>
		  <xsl:with-param name="date1" select="$dates[1]"/>
		  <xsl:with-param name="date2" select="$dates[2]"/>
		</xsl:call-template>
	  </tr>

	</table>
  </div>

</xsl:template>





<xsl:template name="enrolcurrent">
  <xsl:param name="years"></xsl:param>
  <xsl:param name="dates"></xsl:param>
  <xsl:param name="cols"></xsl:param>

  <div class="head">
	<div class="left">
	  <label>Current Academic Year <xsl:value-of select="$years[3]" /> / <xsl:value-of select="$years[2]" /></label>
	</div>
	<div class="right">
	  <label>Enrolments  - <xsl:value-of select="school/value" /></label><xsl:text>&#160; </xsl:text>
	  <label><xsl:value-of select="datestamp" /></label>
	</div>
  </div>

  <div class="spacer"></div>

  <div>
	<table>
	  <tr>
		<th><label></label></th>
		<xsl:call-template name="headerrow">
		  <xsl:with-param name="index" select="1"/>
		  <xsl:with-param name="cols" select="$cols"/>
		</xsl:call-template>
	  </tr>

	  <xsl:apply-templates select="stat[enrolyear/value=$years[2]][date/value=$dates[1]]/groups/group[type='year' or not(type)]">
		<xsl:with-param name="index" select="0"/>
		<xsl:with-param name="cols" select="$cols"/>
		<xsl:with-param name="yearnow" select="$years[2]"/>
		<xsl:with-param name="yearlast" select="$years[3]"/>
		<xsl:with-param name="dates" select="$dates"/>
	  </xsl:apply-templates>

	  <tr>
		<th><label></label></th>
		<xsl:call-template name="headerrow">
		  <xsl:with-param name="index" select="1"/>
		  <xsl:with-param name="cols" select="$cols"/>
		</xsl:call-template>
	  </tr>

	  <xsl:apply-templates select="stat[enrolyear/value=$years[2]][date/value=$dates[1]]/groups/group[type!='year']">
		<xsl:with-param name="index" select="0"/>
		<xsl:with-param name="cols" select="$cols"/>
		<xsl:with-param name="yearnow" select="$years[2]"/>
		<xsl:with-param name="yearlast" select="$years[3]"/>
		<xsl:with-param name="dates" select="$dates"/>
	  </xsl:apply-templates>

	  <tr>
		<th><label>TOTAL</label></th>
		<xsl:call-template name="totalrow">
		  <xsl:with-param name="index" select="1"/>
		  <xsl:with-param name="cols" select="$cols"/>
		  <xsl:with-param name="yearnow" select="$years[2]"/>
		  <xsl:with-param name="yearlast" select="$years[3]"/>
		  <xsl:with-param name="date1" select="$dates[1]"/>
		  <xsl:with-param name="date2" select="$dates[2]"/>
		</xsl:call-template>
	  </tr>

	</table>
  </div>


</xsl:template>




<xsl:template name="enrolnext">
  <xsl:param name="years"></xsl:param>
  <xsl:param name="dates"></xsl:param>
  <xsl:param name="cols"></xsl:param>

  <div class="head">
	<div class="left">
	  <label>Next Academic Year <xsl:value-of select="$years[2]" /> / <xsl:value-of select="$years[1]" /></label>
	</div>
	<div class="right">
	  <label>Enrolments  - <xsl:value-of select="school/value" /></label><xsl:text>&#160; </xsl:text>
	  <label><xsl:value-of select="datestamp" /></label>
	</div>
  </div>

  <div class="spacer"></div>

  <div>
	<table>
	  <tr>
		<th><label></label></th>
		<xsl:call-template name="headerrow">
		  <xsl:with-param name="index" select="1"/>
		  <xsl:with-param name="cols" select="$cols"/>
		</xsl:call-template>
	  </tr>

	  <xsl:apply-templates select="stat[enrolyear/value=$years[1]][date/value=$dates[1]]/groups/group[type='year' or not(type)]">
		<xsl:with-param name="index" select="0"/>
		<xsl:with-param name="cols" select="$cols"/>
		<xsl:with-param name="yearnow" select="$years[1]"/>
		<xsl:with-param name="yearlast" select="$years[2]"/>
		<xsl:with-param name="dates" select="$dates"/>
	  </xsl:apply-templates>

	  <tr>
		<th><label></label></th>
		<xsl:call-template name="headerrow">
		  <xsl:with-param name="index" select="1"/>
		  <xsl:with-param name="cols" select="$cols"/>
		</xsl:call-template>
	  </tr>

	  <xsl:apply-templates select="stat[enrolyear/value=$years[1]][date/value=$dates[1]]/groups/group[type!='year']">
		<xsl:with-param name="index" select="0"/>
		<xsl:with-param name="cols" select="$cols"/>
		<xsl:with-param name="yearnow" select="$years[1]"/>
		<xsl:with-param name="yearlast" select="$years[2]"/>
		<xsl:with-param name="dates" select="$dates"/>
	  </xsl:apply-templates>

	  <tr>
		<th><label>TOTAL</label></th>
		<xsl:call-template name="totalrow">
		  <xsl:with-param name="index" select="1"/>
		  <xsl:with-param name="cols" select="$cols"/>
		  <xsl:with-param name="yearnow" select="$years[1]"/>
		  <xsl:with-param name="yearlast" select="$years[2]"/>
		  <xsl:with-param name="date1" select="$dates[1]"/>
		  <xsl:with-param name="date2" select="$dates[2]"/>
		</xsl:call-template>
	  </tr>

	</table>
  </div>


</xsl:template>



<xsl:template match="group">
  <xsl:param name="index">1</xsl:param>
  <xsl:param name="cols"></xsl:param>
  <xsl:param name="yearnow"></xsl:param>
  <xsl:param name="yearlast"></xsl:param>
  <xsl:param name="dates"></xsl:param>

	  <xsl:choose>
		<xsl:when test="type='subtotal'">
		  <tr class="sub">
			<th>
			  <xsl:value-of select="name" />
			</th>			
			<xsl:call-template name="cell">
			  <xsl:with-param name="index" select="1"/>
			  <xsl:with-param name="cols" select="$cols"/>
			  <xsl:with-param name="yearnow" select="$yearnow"/>
			  <xsl:with-param name="yearlast" select="$yearlast"/>
			  <xsl:with-param name="dates" select="$dates"/>
			</xsl:call-template>
		  </tr>
		</xsl:when>
		<xsl:otherwise>
		  <tr>
			<th>
			  <xsl:value-of select="name" />
			</th>			
			<xsl:call-template name="cell">
			  <xsl:with-param name="index" select="1"/>
			  <xsl:with-param name="cols" select="$cols"/>
			  <xsl:with-param name="yearnow" select="$yearnow"/>
			  <xsl:with-param name="yearlast" select="$yearlast"/>
			  <xsl:with-param name="dates" select="$dates"/>
			</xsl:call-template>
		  </tr>
		</xsl:otherwise>
	  </xsl:choose>
</xsl:template>





<xsl:template name="cell">
  <xsl:param name="index">1</xsl:param>
  <xsl:param name="cols"></xsl:param>
  <xsl:param name="yearnow"></xsl:param>
  <xsl:param name="yearlast"></xsl:param>
  <xsl:param name="dates"></xsl:param>

  <xsl:variable name="maxindex" select="count($cols)" />

  <xsl:if test="$index &lt; $maxindex+1">
	<xsl:variable name="code"><xsl:value-of select="$cols[$index]" />:<xsl:value-of select="id" /></xsl:variable>
	<xsl:variable name="codeap">AP:<xsl:value-of select="id" /></xsl:variable>
	<xsl:variable name="codeleavers1">leaverssince:<xsl:value-of select="id - 1" /></xsl:variable>
	<xsl:variable name="codeleavers2">leavers:<xsl:value-of select="id" /></xsl:variable>


	  <xsl:choose>
		<xsl:when test="$cols[$index]='EN'">
<!--
		  <td class="live"><xsl:text>&#160; </xsl:text>
		  <xsl:value-of select="../../../stat[enrolyear/value=$yearlast][date/value=$dates[2]]/groups/group[type='year' or not(type)]/number[name=$code]/value" />
		  </td>
-->
		  <td class="static"><xsl:text>&#160; </xsl:text>
		  <xsl:value-of select="number[name=$code]/value" />
		  </td>
		</xsl:when>
		<xsl:when test="$cols[$index]='TOTAL'">
		  <td class="live"><xsl:text>&#160; </xsl:text>
		  <xsl:value-of select="../../../stat[enrolyear/value=$yearlast][date/value=$dates[2]]/groups/group[type='year' or not(type)]/number[name=$code]/value" />
		  </td>
		  <td class="other"><xsl:text>&#160; </xsl:text>
		  <xsl:value-of select="number[name=$code]/value" />
		  </td>
		</xsl:when>
		<xsl:when test="$cols[$index]='currentroll' or $cols[$index]='projectedroll'">
		  <td class="other"><xsl:text>&#160; </xsl:text>
		  <xsl:value-of select="number[name=$code]/value" />
		  </td>
		</xsl:when>
		<xsl:when test="$cols[$index]='leavers' or $cols[$index]='transfersout' or $cols[$index]='leaverssince' or $cols[$index]='leaverstotal'">
		  <td class="live"><xsl:text>&#160; </xsl:text>
		  <xsl:value-of select="number[name=$code]/value" />
		  </td>
		</xsl:when>
		<xsl:when test="$cols[$index]='spaces'">
		  <td class="other"><xsl:text>&#160; </xsl:text>
		  <xsl:value-of select="number[name=$code]/value" />
		  </td>
		</xsl:when>
		<xsl:when test="$cols[$index]='budgetroll' or $cols[$index]='targetroll'">
		  <td class="static"><xsl:text>&#160; </xsl:text>
		  <xsl:value-of select="number[name=$code]/value" />
		  </td>
		</xsl:when>
		<xsl:when test="$cols[$index]='AT'">
		  <!-- this is to display both applied and awaiting testing under the same colum -->
		  <td><xsl:text>&#160;</xsl:text>
		  <xsl:value-of select="number[name=$code]/value + number[name=$codeap]/value" />
		  </td>
		</xsl:when>
		<xsl:when test="$cols[$index]='projectedleavers'">
		  <td><xsl:text>&#160;</xsl:text>
		  <xsl:if test="../../../stat/groups/group[type='year' or not(type)]/number[name=$codeleavers1]/value">
			<xsl:value-of select="../../../stat/groups/group[type='year' or not(type)]/number[name=$codeleavers2]/value + ../../../stat/groups/group[type='year' or not(type)]/number[name=$codeleavers1]/value" />
		  </xsl:if>

		  </td>
		</xsl:when>
		<xsl:otherwise>
		  <td><xsl:text>&#160; </xsl:text>
		  <xsl:value-of select="number[name=$code]/value" />
		  </td>
		</xsl:otherwise>
	  </xsl:choose>


    <xsl:call-template name="cell">
	  <xsl:with-param name="index" select="$index+1"/>
	  <xsl:with-param name="cols" select="$cols"/>
	  <xsl:with-param name="yearnow" select="$yearnow"/>
	  <xsl:with-param name="yearlast" select="$yearlast"/>
	  <xsl:with-param name="dates" select="$dates"/>
	</xsl:call-template>
  </xsl:if>
</xsl:template>






<xsl:template name="headerrow">
  <xsl:param name="index">1</xsl:param>
  <xsl:param name="cols"></xsl:param>

  <xsl:variable name="maxindex" select="count($cols)" />

  <xsl:if test="$index &lt; $maxindex+1">

	  <xsl:choose>
		<xsl:when test="$cols[$index]='TOTAL'">
		  <th>
			<xsl:value-of select="tables/table/cols/col[value=$cols[$index]]/name" />
			<br /><label>LAST YEAR</label>
		  </th>
		  <th>
			<xsl:value-of select="tables/table/cols/col[value=$cols[$index]]/name" />
			<br /><label>THIS YEAR</label>
		  </th>
		</xsl:when>
		<xsl:otherwise>
		  <th>
			<xsl:value-of select="tables/table/cols/col[value=$cols[$index]]/name" />
			<xsl:if test="tables/table/cols/col[value=$cols[$index]]/date">
			  <br /><label><xsl:value-of select="tables/table/cols/col[value=$cols[$index]]/date" /></label>
			</xsl:if>
		  </th>
		</xsl:otherwise>
	  </xsl:choose>
  
	<xsl:call-template name="headerrow">
	  <xsl:with-param name="index" select="$index+1"/>
	  <xsl:with-param name="cols" select="$cols"/>
	</xsl:call-template>

  </xsl:if>

</xsl:template>


<xsl:template name="totalrow">
  <xsl:param name="index">1</xsl:param>
  <xsl:param name="cols"></xsl:param>
  <xsl:param name="yearnow"></xsl:param>
  <xsl:param name="yearlast"></xsl:param>
  <xsl:param name="date1"></xsl:param>
  <xsl:param name="date2"></xsl:param>

  <xsl:variable name="maxindex" select="count($cols)" />

  <xsl:if test="$index &lt; $maxindex+1">
	<xsl:variable name="code"><xsl:value-of select="$cols[$index]" />:</xsl:variable>

	  <xsl:choose>
		<xsl:when test="$cols[$index]='EN'">
<!--
		  <td class="live"><xsl:text>&#160; </xsl:text>
		  <xsl:value-of select="sum(stat[enrolyear/value=$yearlast][date/value=$date2]/groups/group[type='year' or not(type)]/number[contains(name,$code)]/value)" />
		  </td>
-->
		  <td class="static"><xsl:text>&#160; </xsl:text>
		  <xsl:value-of select="sum(stat[enrolyear/value=$yearnow][date/value=$date1]/groups/group[type='year' or not(type)]/number[contains(name,$code)]/value)" />
		  </td>
		</xsl:when>
		<xsl:when test="$cols[$index]='TOTAL'">
		  <td class="live"><xsl:text>&#160; </xsl:text>
		  <xsl:value-of select="sum(stat[enrolyear/value=$yearlast][date/value=$date2]/groups/group[type='year' or not(type)]/number[contains(name,$code)]/value)" />
<!--		  <xsl:value-of select="$date2" /> --> 
		  </td>
		  <td class="other"><xsl:text>&#160; </xsl:text>
		  <xsl:value-of select="sum(stat[enrolyear/value=$yearnow][date/value=$date1]/groups/group[type='year' or not(type)]/number[contains(name,$code)]/value)" />
		  </td>
		</xsl:when>
		<xsl:when test="$cols[$index]='currentroll' or $cols[$index]='projectedroll'">
		  <td class="other"><xsl:text>&#160; </xsl:text>
		  <xsl:value-of select="sum(stat[enrolyear/value=$yearnow][date/value=$date1]/groups/group[type='year' or not(type)]/number[contains(name,$code)]/value)" />
		  </td>
		</xsl:when>
		<xsl:when test="$cols[$index]='leavers' or $cols[$index]='transfersout' or $cols[$index]='leaverssince' or $cols[$index]='leaverstotal'">
		  <td class="live"><xsl:text>&#160; </xsl:text>
		  <xsl:value-of select="sum(stat[enrolyear/value=$yearnow][date/value=$date1]/groups/group[type='year' or not(type)]/number[contains(name,$code)]/value)" />
		  </td>
		</xsl:when>
		<xsl:when test="$cols[$index]='transfersout'">
		  <td class="live"><xsl:text>&#160; </xsl:text>
		  <xsl:value-of select="sum(stat[enrolyear/value=$yearnow][date/value=$date1]/groups/group[type='year' or not(type)]/number[contains(name,$code)]/value)" />
		  </td>
		</xsl:when>
		<xsl:when test="$cols[$index]='spaces'">
		  <td class="other"><xsl:text>&#160; </xsl:text>
		  <xsl:value-of select="sum(stat[enrolyear/value=$yearnow][date/value=$date1]/groups/group[type='year' or not(type)]/number[contains(name,$code)]/value)" />
		  </td>
		</xsl:when>
		<xsl:when test="$cols[$index]='budgetroll' or $cols[$index]='targetroll'">
		  <td class="static"><xsl:text>&#160; </xsl:text>
		  <xsl:value-of select="sum(stat[enrolyear/value=$yearnow][date/value=$date1]/groups/group[type='year' or not(type)]/number[contains(name,$code)]/value)" />
		  </td>
		</xsl:when>
		<xsl:when test="$cols[$index]='AT'">
		  <td><xsl:text>&#160; </xsl:text>
		  <xsl:value-of select="sum(stat[enrolyear/value=$yearnow][date/value=$date1]/groups/group[type='year' or not(type)]/number[contains(name,$code)]/value) + sum(stat[enrolyear/value=$yearnow][date/value=$date1]/groups/group[type='year' or not(type)]/number[contains(name,'AP')]/value)" />
		  </td>
		</xsl:when>
		<xsl:when test="$cols[$index]='projectedleavers'">
		  <td><xsl:text>&#160; </xsl:text>
		  <xsl:value-of select="sum(stat[enrolyear/value=$yearlast][date/value=$date1]/groups/group[type='year' or not(type)][position()!=last()]/number[contains(name,'leaverssince:')]/value) + sum(stat[enrolyear/value=$yearnow][date/value=$date1]/groups/group[type='year' or not(type)]/number[contains(name,'leavers:')]/value)" />
<!--
		  <xsl:value-of select="sum(stat[enrolyear/value=$yearlast][date/value=$date1]/groups/group[type='year' or not(type)]/number[contains(name,'leaverssince:') and not(contains(name,':13'))]/value) + sum(stat[enrolyear/value=$yearnow][date/value=$date1]/groups/group[type='year' or not(type)]/number[contains(name,'leavers:')]/value)" />
-->
		  </td>
		</xsl:when>
		<xsl:otherwise>
		  <td>
			<xsl:value-of select="sum(stat[enrolyear/value=$yearnow][date/value=$date1]/groups/group[type='year' or not(type)]/number[contains(name,$code)]/value)" />
		  </td>
		</xsl:otherwise>
	  </xsl:choose>

	<xsl:call-template name="totalrow">
	  <xsl:with-param name="index" select="$index+1"/>
	  <xsl:with-param name="cols" select="$cols"/>
	  <xsl:with-param name="yearnow" select="$yearnow"/>
	  <xsl:with-param name="yearlast" select="$yearlast"/>
	  <xsl:with-param name="date1" select="$date1"/>
	  <xsl:with-param name="date2" select="$date2"/>
	</xsl:call-template>

  </xsl:if>

</xsl:template>







</xsl:stylesheet>
