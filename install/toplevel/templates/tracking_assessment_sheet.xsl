<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"  xmlns:set="http://exslt.org/sets">

<!--  -->

<xsl:output method='html' />

<xsl:template match="students">
  <xsl:variable name="bids" select="set:distinct(student/assessments/assessment/subject/value)" />
  <xsl:variable name="courses" select="set:distinct(student/assessments/assessment/course/value)" />
  <xsl:variable name="labels" select="set:distinct(asstable/ass/printlabel)" />
  <xsl:variable name="dates" select="set:distinct(asstable/ass/date)" />
<!--  <xsl:variable name="elementids" select="set:distinct(asstable/ass/element)" /> -->

  <div class="head">
	<div class="left">
	  <label>Assessment Sheet: </label>
	  <label>
		<xsl:for-each select="$courses">
		  <xsl:value-of select="." /><xsl:text>&#160; </xsl:text>
		</xsl:for-each>
	  </label>
	</div>
	<div class="right">
	  <xsl:value-of select="description/value" />
	</div>
  </div>
  <div class="spacer"></div>

  <div>
	<table>
	  <tr>
		<th colspan="2"><label></label></th>
		<xsl:for-each select="$bids">
		  <th colspan="2">
			<xsl:value-of select="." />
		  </th>
		</xsl:for-each>
		<th><label></label></th>
	  </tr>

	  <tr>
		<th colspan="2"><label></label></th>
		<xsl:for-each select="$bids">
		  <th>
			<xsl:value-of select="substring($labels[1],1,3)" />
		  </th>
		  <th>
			<xsl:value-of select="substring($labels[2],1,3)" />
		  </th>
		</xsl:for-each>
		<th><label></label></th>
	  </tr>

	  <xsl:apply-templates select="student">
		<xsl:with-param name="bids" select="$bids"/>
		<xsl:with-param name="labels" select="$labels"/>
		<xsl:with-param name="dates" select="$dates"/>
	  </xsl:apply-templates>

	</table>
  </div>


<div class='spacer'></div>
<hr />
</xsl:template>

<xsl:template match="student">
  <xsl:param name="bids"></xsl:param>
  <xsl:param name="labels"></xsl:param>
  <xsl:param name="dates"></xsl:param>
	  <tr>
		<th>
			<xsl:value-of select="displayfullsurname/value/text()" />
		</th>
		<th>
			<xsl:value-of select="registrationgroup/value/text()" />
		</th>

		<xsl:call-template name="subjectcell">
		  <xsl:with-param name="index" select="1"/>
		  <xsl:with-param name="bids" select="$bids"/>
		  <xsl:with-param name="labels" select="$labels"/>
		  <xsl:with-param name="dates" select="$dates"/>
		</xsl:call-template>

		<td>
			<table class="subcell">
			  <xsl:for-each select="$dates">
				<tr>
				  <td style="font-size:8pt;font-weight:100;">
					<xsl:value-of select="substring(.,1,7)" />
				  </td>
				</tr>
			  </xsl:for-each>
			</table>
		</td>
	  </tr>
</xsl:template>


<xsl:template name="subjectcell">
  <xsl:param name="index">1</xsl:param>
  <xsl:param name="bids"></xsl:param>
  <xsl:param name="labels"></xsl:param>
  <xsl:param name="dates"></xsl:param>

  <xsl:variable name="maxindex" select="count($bids)" />

  <xsl:if test="$index &lt; $maxindex+1">

	<td colspan="2">
	  <table class="subcell">
	  <xsl:call-template name="assrow">
		<xsl:with-param name="index" select="1"/>
		<xsl:with-param name="bid" select="$bids[$index]"/>
		<xsl:with-param name="labels" select="$labels"/>
		<xsl:with-param name="dates" select="$dates"/>
	  </xsl:call-template>
	  </table>
	</td>
  
    <xsl:call-template name="subjectcell">
	  <xsl:with-param name="index" select="$index+1"/>
	  <xsl:with-param name="bids" select="$bids"/>
	  <xsl:with-param name="labels" select="$labels"/>
	  <xsl:with-param name="dates" select="$dates"/>
	</xsl:call-template>

  </xsl:if>

</xsl:template>



<xsl:template name="assrow">
  <xsl:param name="index">1</xsl:param>
  <xsl:param name="bid"></xsl:param>
  <xsl:param name="labels"></xsl:param>
  <xsl:param name="dates"></xsl:param>

  <xsl:variable name="maxindex" select="count($dates)" />

  <xsl:if test="$index &lt; $maxindex+1">

	<tr>
		<xsl:call-template name="asscell">
		  <xsl:with-param name="bid" select="$bid"/>
		  <xsl:with-param name="element" select="../asstable/ass[date=string($dates[$index])][printlabel=$labels[1]]/element"/>
		</xsl:call-template>
		<xsl:call-template name="asscell">
		  <xsl:with-param name="bid" select="$bid"/>
		  <xsl:with-param name="element" select="../asstable/ass[date=string($dates[$index])][printlabel=$labels[2]]/element"/>
		</xsl:call-template>
	</tr>
		  
    <xsl:call-template name="assrow">
	  <xsl:with-param name="index" select="$index + 1"/>
	  <xsl:with-param name="bid" select="$bid"/>
	  <xsl:with-param name="labels" select="$labels"/>
	  <xsl:with-param name="dates" select="$dates"/>
	</xsl:call-template>

  </xsl:if>

</xsl:template>




<xsl:template name="asscell">
  <xsl:param name="bid"></xsl:param>
  <xsl:param name="element"></xsl:param>

  <td>
	<xsl:text>&#160; </xsl:text>

<!--	<xsl:value-of select="assessments/assessment[id_db=$id_db][subject/value=$bid]/result/value" /> -->
	<xsl:value-of select="assessments/assessment[element=string($element)][subject/value=string($bid)]/result/value" />
  </td>

</xsl:template>



</xsl:stylesheet>
